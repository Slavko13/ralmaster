#!/usr/bin/env bash

set -Eeuo pipefail
IFS=$'\n\t'
umask 077

PROGRAM_NAME="$(basename "$0")"
STATE_DIR="/root/ralmaster"
APP_DIR="${STATE_DIR}/app"
ENV_FILE="${STATE_DIR}/.env.production"
LOG_FILE="${STATE_DIR}/deploy.log"

DOMAIN="test.ral-master.ru"
APP_PORT="8081"
COMPOSE_PROJECT="ralmaster-test"
GIT_REF="main"
REPO_URL="${RALMASTER_GIT_URL:-}"
DB_BACKUP=""
FILES_BACKUP=""
CADDYFILE="/etc/caddy/Caddyfile"
CADDYFILE_EXPLICIT=0
CADDY_CONTAINER=""
CADDY_NETWORK=""
CADDY_MODE=""
CADDY_CONTAINER_CONFIG="/etc/caddy/Caddyfile"
CADDY_PROXY_ALIAS=""
CADDY_COMPOSE_OVERRIDE=""
ASSUME_YES=0
SKIP_CADDY=0
RESTORE_FILES=0
TMP_RESTORE_DIR=""

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

warn() {
  printf '[%s] WARNING: %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" >&2
}

die() {
  printf '[%s] ERROR: %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" >&2
  exit 1
}

usage() {
  cat <<EOF
Usage:
  ${PROGRAM_NAME} --repo <git-url> [options]

Required for the first deployment:
  --repo URL                 Git SSH/HTTPS URL without embedded credentials.

Options:
  --ref REF                  Git branch to deploy (default: ${GIT_REF}).
  --domain DOMAIN            Staging hostname (default: ${DOMAIN}).
  --port PORT                Free loopback port for Apache (default: ${APP_PORT}).
  --project NAME             Docker Compose project (default: ${COMPOSE_PROJECT}).
  --state-dir PATH           Persistent deployment directory (default: ${STATE_DIR}).
  --db-backup PATH           MySQL .sql, .sql.gz, .dump or .dump.gz file.
  --files-backup PATH        Beget file backup in .tar, .tar.gz, .tgz or .zip.
  --caddyfile PATH           Host path to Caddyfile (auto-detected for Docker Caddy).
  --caddy-container NAME     Existing Docker Caddy container (auto-detected).
  --caddy-network NAME       Existing Docker network used by Caddy (auto-detected).
  --restore-files            Restore bitrix/upload even if already present.
  --skip-caddy               Deploy containers without changing Caddy.
  --yes                      Do not ask for the final interactive confirmation.
  -h, --help                 Show this help.

Expected layout after deployment:
  <state-dir>/app             Git checkout
  <state-dir>/.env.production root-only Docker secrets
  <state-dir>/deploy.log      deployment log

The script never overwrites a non-empty database and never deletes Docker
volumes. It only manages the Caddy block marked for the selected Compose project.
EOF
}

while (($#)); do
  case "$1" in
    --repo)
      (($# >= 2)) || die '--repo requires a value'
      REPO_URL="$2"
      shift 2
      ;;
    --ref)
      (($# >= 2)) || die '--ref requires a value'
      GIT_REF="$2"
      shift 2
      ;;
    --domain)
      (($# >= 2)) || die '--domain requires a value'
      DOMAIN="$2"
      shift 2
      ;;
    --port)
      (($# >= 2)) || die '--port requires a value'
      APP_PORT="$2"
      shift 2
      ;;
    --project)
      (($# >= 2)) || die '--project requires a value'
      COMPOSE_PROJECT="$2"
      shift 2
      ;;
    --state-dir)
      (($# >= 2)) || die '--state-dir requires a value'
      STATE_DIR="${2%/}"
      APP_DIR="${STATE_DIR}/app"
      ENV_FILE="${STATE_DIR}/.env.production"
      LOG_FILE="${STATE_DIR}/deploy.log"
      shift 2
      ;;
    --db-backup)
      (($# >= 2)) || die '--db-backup requires a value'
      DB_BACKUP="$2"
      shift 2
      ;;
    --files-backup)
      (($# >= 2)) || die '--files-backup requires a value'
      FILES_BACKUP="$2"
      shift 2
      ;;
    --caddyfile)
      (($# >= 2)) || die '--caddyfile requires a value'
      CADDYFILE="$2"
      CADDYFILE_EXPLICIT=1
      shift 2
      ;;
    --caddy-container)
      (($# >= 2)) || die '--caddy-container requires a value'
      CADDY_CONTAINER="$2"
      shift 2
      ;;
    --caddy-network)
      (($# >= 2)) || die '--caddy-network requires a value'
      CADDY_NETWORK="$2"
      shift 2
      ;;
    --restore-files)
      RESTORE_FILES=1
      shift
      ;;
    --skip-caddy)
      SKIP_CADDY=1
      shift
      ;;
    --yes)
      ASSUME_YES=1
      shift
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      die "unknown option: $1"
      ;;
  esac
done

cleanup() {
  if [[ -n "${TMP_RESTORE_DIR}" && -d "${TMP_RESTORE_DIR}" && "${TMP_RESTORE_DIR}" == "${STATE_DIR}"/.restore.* ]]; then
    rm -rf -- "${TMP_RESTORE_DIR}"
  fi
}

on_error() {
  local exit_code=$?
  local line_no=${1:-unknown}
  warn "deployment stopped at line ${line_no} with exit code ${exit_code}"
  exit "${exit_code}"
}

trap cleanup EXIT
trap 'on_error $LINENO' ERR

[[ ${EUID} -eq 0 ]] || die 'run this script as root'
[[ "${STATE_DIR}" == /root/* || "${STATE_DIR}" == /srv/* ]] || die '--state-dir must be an explicit directory below /root or /srv'
[[ "${DOMAIN}" =~ ^[A-Za-z0-9]([A-Za-z0-9.-]*[A-Za-z0-9])?$ ]] || die 'invalid domain name'
[[ "${APP_PORT}" =~ ^[0-9]+$ ]] || die 'port must be numeric'
((APP_PORT >= 1024 && APP_PORT <= 65535)) || die 'port must be between 1024 and 65535'
[[ "${COMPOSE_PROJECT}" =~ ^[a-z0-9][a-z0-9_-]*$ ]] || die 'invalid Compose project name'
[[ -n "${GIT_REF}" && "${GIT_REF}" != -* ]] || die 'invalid Git ref'

if [[ "${REPO_URL}" =~ ^https?://[^/]*@ ]]; then
  die 'do not put a password or token in the Git URL; use an SSH deploy key or a credential helper'
fi

mkdir -p -- "${STATE_DIR}"
chmod 700 -- "${STATE_DIR}"
touch -- "${LOG_FILE}"
chmod 600 -- "${LOG_FILE}"
exec > >(tee -a "${LOG_FILE}") 2>&1

ensure_packages() {
  local missing=()
  local command_name

  for command_name in git curl rsync openssl tar gzip unzip ss awk sed find; do
    command -v "${command_name}" >/dev/null 2>&1 || missing+=("${command_name}")
  done

  ((${#missing[@]} == 0)) && return
  command -v apt-get >/dev/null 2>&1 || die "missing commands (${missing[*]}) and apt-get is unavailable"

  log "Installing required operating-system packages: ${missing[*]}"
  export DEBIAN_FRONTEND=noninteractive
  apt-get update
  apt-get install -y --no-install-recommends \
    ca-certificates curl git gzip iproute2 openssl rsync tar unzip
}

ensure_packages
command -v docker >/dev/null 2>&1 || die 'Docker is not installed'
docker info >/dev/null 2>&1 || die 'Docker Engine is not running or is inaccessible'
docker compose version >/dev/null 2>&1 || die 'Docker Compose v2 plugin is not installed'
git check-ref-format --branch "${GIT_REF}" >/dev/null 2>&1 || die 'invalid Git branch name'

detect_caddy() {
  if ((SKIP_CADDY == 1)); then
    CADDY_MODE="skipped"
    return
  fi

  if command -v caddy >/dev/null 2>&1 && systemctl is-active --quiet caddy; then
    [[ -f "${CADDYFILE}" ]] || die "Caddy is active, but ${CADDYFILE} does not exist"
    CADDY_MODE="host"
    return
  fi

  local -a candidates=()
  local -a networks=()
  local caddy_project=""
  local preferred_network=""
  local network_name=""
  local mount_line=""
  local mount_type=""
  local mount_source=""
  local mount_destination=""

  if [[ -n "${CADDY_CONTAINER}" ]]; then
    docker inspect "${CADDY_CONTAINER}" >/dev/null 2>&1 || die "Caddy container does not exist: ${CADDY_CONTAINER}"
    [[ "$(docker inspect --format '{{.State.Running}}' "${CADDY_CONTAINER}")" == "true" ]] || die "Caddy container is not running: ${CADDY_CONTAINER}"
  else
    mapfile -t candidates < <(docker ps --format '{{.Names}}|{{.Image}}' | awk -F'|' 'tolower($1 " " $2) ~ /caddy/ {print $1}')
    ((${#candidates[@]} > 0)) || die 'an active host or Docker Caddy instance was not found; refusing to install a second proxy on ports 80/443'
    ((${#candidates[@]} == 1)) || {
      printf 'Running Caddy containers:\n  %s\n' "${candidates[@]}" >&2
      die 'multiple Caddy containers were found; select one with --caddy-container'
    }
    CADDY_CONTAINER="${candidates[0]}"
  fi

  if [[ -z "${CADDY_NETWORK}" ]]; then
    mapfile -t networks < <(
      docker inspect --format '{{range $name, $_ := .NetworkSettings.Networks}}{{println $name}}{{end}}' "${CADDY_CONTAINER}" |
        awk 'NF && $0 != "bridge" && $0 != "host" && $0 != "none"'
    )
    ((${#networks[@]} > 0)) || die "Caddy container ${CADDY_CONTAINER} has no user-defined Docker network"
    if ((${#networks[@]} == 1)); then
      CADDY_NETWORK="${networks[0]}"
    else
      caddy_project="$(docker inspect --format '{{index .Config.Labels "com.docker.compose.project"}}' "${CADDY_CONTAINER}" 2>/dev/null || true)"
      preferred_network="${caddy_project}_default"
      for network_name in "${networks[@]}"; do
        if [[ -n "${caddy_project}" && "${network_name}" == "${preferred_network}" ]]; then
          CADDY_NETWORK="${network_name}"
          break
        fi
      done
      if [[ -z "${CADDY_NETWORK}" ]]; then
        printf 'Networks attached to %s:\n  %s\n' "${CADDY_CONTAINER}" "${networks[@]}" >&2
        die 'Caddy uses multiple Docker networks; select one with --caddy-network'
      fi
    fi
  fi

  [[ "${CADDY_NETWORK}" =~ ^[A-Za-z0-9][A-Za-z0-9_.-]*$ ]] || die 'invalid Caddy Docker network name'
  docker network inspect "${CADDY_NETWORK}" >/dev/null 2>&1 || die "Caddy Docker network does not exist: ${CADDY_NETWORK}"
  docker inspect --format '{{range $name, $_ := .NetworkSettings.Networks}}{{println $name}}{{end}}' "${CADDY_CONTAINER}" |
    grep -Fxq "${CADDY_NETWORK}" || die "Caddy container is not attached to network ${CADDY_NETWORK}"

  if ((CADDYFILE_EXPLICIT == 0)); then
    mount_line="$(docker inspect --format '{{range .Mounts}}{{printf "%s|%s|%s\n" .Type .Source .Destination}}{{end}}' "${CADDY_CONTAINER}" |
      awk -F'|' '$3 == "/etc/caddy/Caddyfile" {print; exit}')"
    if [[ -n "${mount_line}" ]]; then
      IFS='|' read -r mount_type mount_source mount_destination <<< "${mount_line}"
      CADDYFILE="${mount_source}"
    else
      mount_line="$(docker inspect --format '{{range .Mounts}}{{printf "%s|%s|%s\n" .Type .Source .Destination}}{{end}}' "${CADDY_CONTAINER}" |
        awk -F'|' '$3 == "/etc/caddy" {print; exit}')"
      if [[ -n "${mount_line}" ]]; then
        IFS='|' read -r mount_type mount_source mount_destination <<< "${mount_line}"
        CADDYFILE="${mount_source}/Caddyfile"
      fi
    fi
  fi

  [[ -f "${CADDYFILE}" ]] || {
    docker inspect --format '{{range .Mounts}}{{println .Type .Source "->" .Destination}}{{end}}' "${CADDY_CONTAINER}" >&2
    die "unable to locate the host-mounted Caddyfile for ${CADDY_CONTAINER}; pass its host path with --caddyfile"
  }
  [[ -w "${CADDYFILE}" ]] || die "Caddyfile is not writable: ${CADDYFILE}"

  CADDY_PROXY_ALIAS="${COMPOSE_PROJECT//_/-}-web"
  [[ "${CADDY_PROXY_ALIAS}" =~ ^[a-z0-9][a-z0-9.-]*$ ]] || die 'the Compose project name cannot be converted to a safe proxy alias'
  CADDY_MODE="container"
}

detect_caddy

port_listener() {
  ss -H -ltnp 2>/dev/null | awk -v port=":${APP_PORT}" '$4 ~ port "$" {print}'
}

port_is_owned_by_project() {
  local container_id
  container_id="$(docker ps \
    --filter "label=com.docker.compose.project=${COMPOSE_PROJECT}" \
    --filter 'label=com.docker.compose.service=web' \
    --quiet | head -n 1)"
  [[ -n "${container_id}" ]] || return 1
  docker port "${container_id}" 80/tcp 2>/dev/null | grep -Eq "(^|:)${APP_PORT}$"
}

if [[ -n "$(port_listener)" ]] && ! port_is_owned_by_project; then
  port_listener >&2
  die "127.0.0.1:${APP_PORT} is already occupied by another process; select another --port"
fi

discover_single_file() {
  local label="$1"
  shift
  local -a matches=()
  local pattern

  for pattern in "$@"; do
    while IFS= read -r -d '' match; do
      matches+=("${match}")
    done < <(find "${STATE_DIR}" -maxdepth 1 -type f -iname "${pattern}" -print0)
  done

  if ((${#matches[@]} == 1)); then
    printf '%s' "${matches[0]}"
    return
  fi
  if ((${#matches[@]} > 1)); then
    printf 'Multiple %s candidates found:\n' "${label}" >&2
    printf '  %s\n' "${matches[@]}" >&2
    die "pass the exact ${label} path explicitly"
  fi
  return 1
}

if [[ -z "${DB_BACKUP}" ]]; then
  DB_BACKUP="$(discover_single_file 'database backup' '*.sql' '*.sql.gz' '*.dump' '*.dump.gz' || true)"
fi
if [[ -z "${FILES_BACKUP}" ]]; then
  FILES_BACKUP="$(discover_single_file 'file backup' '*.tar' '*.tar.gz' '*.tgz' '*.zip' || true)"
fi

[[ -z "${DB_BACKUP}" || -f "${DB_BACKUP}" ]] || die "database backup not found: ${DB_BACKUP}"
[[ -z "${FILES_BACKUP}" || -f "${FILES_BACKUP}" ]] || die "file backup not found: ${FILES_BACKUP}"

log 'Deployment plan:'
log "  domain:          ${DOMAIN}"
log "  loopback port:   127.0.0.1:${APP_PORT}"
log "  Compose project: ${COMPOSE_PROJECT}"
log "  Git ref:         ${GIT_REF}"
log "  state directory: ${STATE_DIR}"
log "  database backup: ${DB_BACKUP:-not auto-detected}"
log "  file backup:     ${FILES_BACKUP:-not auto-detected}"
case "${CADDY_MODE}" in
  host)
    log "  Caddy:           host service (${CADDYFILE})"
    ;;
  container)
    log "  Caddy:           Docker container ${CADDY_CONTAINER}"
    log "  Caddy network:   ${CADDY_NETWORK}"
    log "  Caddyfile:        ${CADDYFILE}"
    log "  proxy upstream:   ${CADDY_PROXY_ALIAS}:80"
    ;;
  skipped)
    log '  Caddy:           skipped by request'
    ;;
esac

if ((ASSUME_YES == 0)); then
  [[ -t 0 ]] || die 'non-interactive execution requires --yes'
  read -r -p 'Continue with this deployment plan? [y/N] ' confirmation
  [[ "${confirmation}" == 'y' || "${confirmation}" == 'Y' ]] || die 'deployment cancelled'
fi

checkout_source() {
  export GIT_TERMINAL_PROMPT=0

  if [[ -d "${APP_DIR}/.git" ]]; then
    local existing_remote
    existing_remote="$(git -C "${APP_DIR}" remote get-url origin)"
    if [[ -n "${REPO_URL}" && "${existing_remote}" != "${REPO_URL}" ]]; then
      die "existing checkout origin differs from --repo (${existing_remote})"
    fi
    [[ -z "$(git -C "${APP_DIR}" status --porcelain --untracked-files=no)" ]] || die 'tracked files in the deployment checkout are modified; refusing to overwrite them'

    log "Fetching ${GIT_REF} from Git"
    git -C "${APP_DIR}" fetch --prune origin \
      "+refs/heads/${GIT_REF}:refs/remotes/origin/${GIT_REF}"
    if git -C "${APP_DIR}" show-ref --verify --quiet "refs/heads/${GIT_REF}"; then
      git -C "${APP_DIR}" checkout "${GIT_REF}"
    else
      git -C "${APP_DIR}" checkout --track -b "${GIT_REF}" "origin/${GIT_REF}"
    fi
    git -C "${APP_DIR}" merge --ff-only "origin/${GIT_REF}"
    return
  fi

  [[ ! -e "${APP_DIR}" || -z "$(find "${APP_DIR}" -mindepth 1 -maxdepth 1 -print -quit 2>/dev/null)" ]] || die "${APP_DIR} exists and is not an empty Git checkout"
  [[ -n "${REPO_URL}" ]] || die '--repo is required for the first deployment'
  mkdir -p -- "$(dirname "${APP_DIR}")"
  log "Cloning ${GIT_REF} into ${APP_DIR}"
  git clone --branch "${GIT_REF}" --single-branch "${REPO_URL}" "${APP_DIR}"
}

checkout_source
REPO_URL="$(git -C "${APP_DIR}" remote get-url origin)"
[[ -f "${APP_DIR}/compose.production.yml" ]] || die 'compose.production.yml is missing from the Git checkout'
[[ -f "${APP_DIR}/Dockerfile" ]] || die 'Dockerfile is missing from the Git checkout'

archive_has_unsafe_paths() {
  awk '
    /^\// { unsafe=1 }
    {
      count=split($0, parts, "/")
      for (i=1; i<=count; i++) if (parts[i] == "..") unsafe=1
    }
    END { exit unsafe ? 0 : 1 }
  '
}

restore_file_backup() {
  local needs_core=0
  local needs_upload=0
  [[ -f "${APP_DIR}/bitrix/modules/main/include.php" ]] || needs_core=1
  [[ -d "${APP_DIR}/upload" && -n "$(find "${APP_DIR}/upload" -type f ! -name '.gitkeep' -print -quit 2>/dev/null)" ]] || needs_upload=1

  if ((RESTORE_FILES == 0 && needs_core == 0 && needs_upload == 0)); then
    log 'Bitrix core and upload files already exist; file archive restore skipped'
    return
  fi
  [[ -n "${FILES_BACKUP}" ]] || die 'Bitrix core/upload are absent. Download the Beget file archive and pass --files-backup.'

  chmod 600 -- "${FILES_BACKUP}"
  TMP_RESTORE_DIR="$(mktemp -d "${STATE_DIR}/.restore.XXXXXX")"
  log "Validating and extracting file backup ${FILES_BACKUP}"

  case "${FILES_BACKUP,,}" in
    *.tar.gz|*.tgz)
      if tar -tzf "${FILES_BACKUP}" | archive_has_unsafe_paths; then
        die 'file archive contains an absolute path or parent-directory traversal'
      fi
      tar -xzf "${FILES_BACKUP}" -C "${TMP_RESTORE_DIR}"
      ;;
    *.tar)
      if tar -tf "${FILES_BACKUP}" | archive_has_unsafe_paths; then
        die 'file archive contains an absolute path or parent-directory traversal'
      fi
      tar -xf "${FILES_BACKUP}" -C "${TMP_RESTORE_DIR}"
      ;;
    *.zip)
      if unzip -Z1 "${FILES_BACKUP}" | archive_has_unsafe_paths; then
        die 'file archive contains an absolute path or parent-directory traversal'
      fi
      unzip -q "${FILES_BACKUP}" -d "${TMP_RESTORE_DIR}"
      ;;
    *)
      die 'unsupported file archive; use .tar, .tar.gz, .tgz or .zip'
      ;;
  esac

  local -a core_markers=()
  while IFS= read -r -d '' marker; do
    core_markers+=("${marker}")
  done < <(find "${TMP_RESTORE_DIR}" -type f -path '*/bitrix/modules/main/include.php' -print0)
  ((${#core_markers[@]} == 1)) || die "expected exactly one Bitrix site in the file archive; found ${#core_markers[@]}"

  local bitrix_source
  local site_source
  bitrix_source="${core_markers[0]%/modules/main/include.php}"
  site_source="$(dirname "${bitrix_source}")"
  [[ -d "${site_source}/upload" ]] || die 'the file archive contains Bitrix core but no sibling upload directory'

  mkdir -p -- "${APP_DIR}/bitrix" "${APP_DIR}/upload"
  if ((RESTORE_FILES == 1 || needs_core == 1)); then
    log 'Restoring Bitrix core from the file backup'
    rsync -a \
      --exclude '/cache/***' \
      --exclude '/managed_cache/***' \
      --exclude '/stack_cache/***' \
      "${bitrix_source}/" "${APP_DIR}/bitrix/"
  fi
  if ((RESTORE_FILES == 1 || needs_upload == 1)); then
    log 'Restoring upload files from the file backup'
    rsync -a "${site_source}/upload/" "${APP_DIR}/upload/"
  fi
  if [[ -f "${site_source}/include/contacts-site-map.php" && ! -f "${APP_DIR}/include/contacts-site-map.php" ]]; then
    mkdir -p -- "${APP_DIR}/include"
    install -m 0644 "${site_source}/include/contacts-site-map.php" "${APP_DIR}/include/contacts-site-map.php"
  fi

  [[ -f "${APP_DIR}/bitrix/modules/main/include.php" ]] || die 'Bitrix core restore verification failed'
  [[ -n "$(find "${APP_DIR}/upload" -type f ! -name '.gitkeep' -print -quit)" ]] || die 'upload restore verification failed'
}

restore_file_backup

install_bitrix_runtime_config() {
  local settings_source="${APP_DIR}/docker/bitrix/.settings_extra.php"
  local dbconn_source="${APP_DIR}/docker/bitrix/dbconn.php"

  [[ -f "${settings_source}" ]] || die 'docker/bitrix/.settings_extra.php is missing from the Git checkout'
  [[ -f "${dbconn_source}" ]] || die 'docker/bitrix/dbconn.php is missing from the Git checkout'

  mkdir -p -- "${APP_DIR}/bitrix/php_interface"
  install -m 0640 -- "${settings_source}" "${APP_DIR}/bitrix/.settings_extra.php"
  install -m 0640 -- "${dbconn_source}" "${APP_DIR}/bitrix/php_interface/dbconn.php"
  log 'Installed environment-based Bitrix database configuration into the restored core'
}

install_bitrix_runtime_config

normalize_application_permissions() {
  # The deployment script itself uses umask 077 so secrets created below stay
  # root-only. Bind-mounted application files need narrower, explicit access:
  # Apache runs as uid/gid 33, while the MySQL image uses its own mysql uid.
  chown -R root:33 -- "${APP_DIR}"
  chmod -R u+rwX,g+rX,o-rwx -- "${APP_DIR}"

  if [[ -d "${APP_DIR}/.git" ]]; then
    chown -R root:root -- "${APP_DIR}/.git"
    chmod -R go-rwx -- "${APP_DIR}/.git"
  fi

  [[ -d "${APP_DIR}/docker/db/init" ]] || die 'docker/db/init is missing from the Git checkout'
  chmod 0755 -- "${APP_DIR}/docker/db/init"
  find "${APP_DIR}/docker/db/init" -maxdepth 1 -type f -exec chmod 0644 -- {} +
}

normalize_application_permissions
mkdir -p -- \
  "${APP_DIR}/bitrix/cache" \
  "${APP_DIR}/bitrix/managed_cache" \
  "${APP_DIR}/bitrix/stack_cache" \
  "${APP_DIR}/local/php_interface/logs" \
  "${APP_DIR}/upload"
chown -R 33:33 -- "${APP_DIR}/upload"
chmod -R u+rwX,go+rX -- "${APP_DIR}/upload"

create_environment() {
  if [[ -f "${ENV_FILE}" ]]; then
    local saved_port
    local saved_project
    saved_port="$(sed -n 's/^APP_PROD_PORT=//p' "${ENV_FILE}" | tail -n 1)"
    saved_project="$(sed -n 's/^PROD_COMPOSE_PROJECT_NAME=//p' "${ENV_FILE}" | tail -n 1)"
    [[ "${saved_port}" == "${APP_PORT}" ]] || die "existing ${ENV_FILE} uses APP_PROD_PORT=${saved_port}; requested ${APP_PORT}"
    [[ "${saved_project}" == "${COMPOSE_PROJECT}" ]] || die "existing ${ENV_FILE} uses project ${saved_project}; requested ${COMPOSE_PROJECT}"
    log "Reusing root-only environment file ${ENV_FILE}"
    return
  fi

  local db_password
  local db_root_password
  db_password="$(openssl rand -hex 32)"
  db_root_password="$(openssl rand -hex 32)"

  cat > "${ENV_FILE}" <<EOF
PROD_COMPOSE_PROJECT_NAME=${COMPOSE_PROJECT}
APP_PROD_PORT=${APP_PORT}
TZ=Europe/Moscow
BITRIX_DB_HOST=db
BITRIX_DB_PORT=3306
BITRIX_DB_NAME=bitrix
BITRIX_DB_USER=bitrix
BITRIX_DB_PASSWORD=${db_password}
BITRIX_DB_ROOT_PASSWORD=${db_root_password}
EOF
  chmod 600 -- "${ENV_FILE}"
  log "Created root-only environment file ${ENV_FILE}"
}

create_environment

COMPOSE=(
  docker compose
  --env-file "${ENV_FILE}"
  -p "${COMPOSE_PROJECT}"
  -f "${APP_DIR}/compose.production.yml"
)

if [[ "${CADDY_MODE}" == "container" ]]; then
  CADDY_COMPOSE_OVERRIDE="${STATE_DIR}/compose.caddy-proxy.yml"
  cat > "${CADDY_COMPOSE_OVERRIDE}" <<EOF
services:
  web:
    networks:
      default: {}
      caddy_proxy:
        aliases:
          - ${CADDY_PROXY_ALIAS}

networks:
  caddy_proxy:
    external: true
    name: ${CADDY_NETWORK}
EOF
  chmod 600 -- "${CADDY_COMPOSE_OVERRIDE}"
  COMPOSE+=( -f "${CADDY_COMPOSE_OVERRIDE}" )
fi

"${COMPOSE[@]}" config --quiet
log 'Starting the isolated MySQL service'
"${COMPOSE[@]}" up -d --wait db

database_table_count() {
  "${COMPOSE[@]}" exec -T db sh -lc \
    'exec env MYSQL_PWD="$MYSQL_PASSWORD" mysql --default-character-set=utf8mb4 -N -s -u"$MYSQL_USER" "$MYSQL_DATABASE" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE();"'
}

stream_database_backup() {
  case "${DB_BACKUP,,}" in
    *.sql.gz|*.dump.gz)
      gzip -dc -- "${DB_BACKUP}"
      ;;
    *.sql|*.dump)
      cat -- "${DB_BACKUP}"
      ;;
    *)
      die 'unsupported database backup; use .sql, .sql.gz, .dump or .dump.gz'
      ;;
  esac
}

table_count="$(database_table_count)"
[[ "${table_count}" =~ ^[0-9]+$ ]] || die 'unable to read the database table count'

if ((table_count == 0)); then
  [[ -n "${DB_BACKUP}" ]] || die 'the database is empty and no SQL backup was found; pass --db-backup'
  chmod 600 -- "${DB_BACKUP}"
  log "Importing the database backup into the empty ${COMPOSE_PROJECT} volume"
  stream_database_backup | "${COMPOSE[@]}" exec -T db sh -lc \
    'exec env MYSQL_PWD="$MYSQL_PASSWORD" mysql --default-character-set=utf8mb4 -u"$MYSQL_USER" "$MYSQL_DATABASE"'
  table_count="$(database_table_count)"
  [[ "${table_count}" =~ ^[0-9]+$ ]] || die 'unable to verify the imported database'
  ((table_count >= 100)) || die "database import produced only ${table_count} tables; expected at least 100"
  sha256sum -- "${DB_BACKUP}" > "${STATE_DIR}/database-backup.sha256"
  chmod 600 -- "${STATE_DIR}/database-backup.sha256"
  log "Database import verified: ${table_count} tables"
else
  log "Database already contains ${table_count} tables; SQL import skipped"
fi

log 'Building and starting the production-like web service'
"${COMPOSE[@]}" up -d --build --wait web
curl --fail --silent --show-error --max-time 15 "http://127.0.0.1:${APP_PORT}/healthz.php" >/dev/null
log 'Container health endpoint is available'

if [[ -f "${APP_DIR}/scripts/bitrix/migrate-ralmaster-estimate-form.php" ]]; then
  log 'Applying the idempotent RALMASTER estimate-form migration'
  "${COMPOSE[@]}" exec -T web php scripts/bitrix/migrate-ralmaster-estimate-form.php --apply
  "${COMPOSE[@]}" exec -T web php scripts/bitrix/migrate-ralmaster-estimate-form.php --verify
fi

"${COMPOSE[@]}" exec -T web sh -lc '
  for cache_dir in \
    /var/www/html/bitrix/cache \
    /var/www/html/bitrix/managed_cache \
    /var/www/html/bitrix/stack_cache
  do
    [ -d "$cache_dir" ] || continue
    find "$cache_dir" -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +
  done
'

configure_caddy() {
  ((SKIP_CADDY == 1)) && return

  local marker_start="# BEGIN MANAGED ${COMPOSE_PROJECT}"
  local marker_end="# END MANAGED ${COMPOSE_PROJECT}"
  local start_count
  local end_count
  local backup_file
  local candidate_file
  local upstream="127.0.0.1:${APP_PORT}"

  if [[ "${CADDY_MODE}" == "container" ]]; then
    upstream="${CADDY_PROXY_ALIAS}:80"
  fi

  start_count="$(grep -Fxc "${marker_start}" "${CADDYFILE}" || true)"
  end_count="$(grep -Fxc "${marker_end}" "${CADDYFILE}" || true)"
  [[ "${start_count}" == "${end_count}" ]] || die 'Caddy managed-block markers are unbalanced'
  ((start_count <= 1)) || die 'multiple managed Caddy blocks were found'

  backup_file="${STATE_DIR}/Caddyfile.$(date '+%Y%m%d-%H%M%S').bak"
  candidate_file="$(mktemp "${STATE_DIR}/.Caddyfile.XXXXXX")"
  cp -a -- "${CADDYFILE}" "${backup_file}"

  awk -v start="${marker_start}" -v end="${marker_end}" '
    $0 == start { skipping=1; next }
    $0 == end { skipping=0; next }
    !skipping { print }
  ' "${CADDYFILE}" > "${candidate_file}"

  cat >> "${candidate_file}" <<EOF

${marker_start}
${DOMAIN} {
    encode zstd gzip
    reverse_proxy ${upstream}
}
${marker_end}
EOF

  cat -- "${candidate_file}" > "${CADDYFILE}"
  chmod --reference="${backup_file}" "${CADDYFILE}"
  chown --reference="${backup_file}" "${CADDYFILE}"
  rm -f -- "${candidate_file}"

  if [[ "${CADDY_MODE}" == "host" ]]; then
    if ! caddy validate --config "${CADDYFILE}" --adapter caddyfile; then
      cat -- "${backup_file}" > "${CADDYFILE}"
      die "Caddy validation failed; restored ${backup_file}"
    fi
    if ! systemctl reload caddy; then
      cat -- "${backup_file}" > "${CADDYFILE}"
      systemctl reload caddy || true
      die "Caddy reload failed; restored ${backup_file}"
    fi
  else
    if ! docker exec "${CADDY_CONTAINER}" caddy validate --config "${CADDY_CONTAINER_CONFIG}" --adapter caddyfile; then
      cat -- "${backup_file}" > "${CADDYFILE}"
      die "Caddy validation failed inside ${CADDY_CONTAINER}; restored ${backup_file}"
    fi
    if ! docker exec "${CADDY_CONTAINER}" caddy reload --config "${CADDY_CONTAINER_CONFIG}" --adapter caddyfile; then
      cat -- "${backup_file}" > "${CADDYFILE}"
      docker exec "${CADDY_CONTAINER}" caddy reload --config "${CADDY_CONTAINER_CONFIG}" --adapter caddyfile || true
      die "Caddy reload failed inside ${CADDY_CONTAINER}; restored ${backup_file}"
    fi
  fi
  log "Caddy route installed for ${DOMAIN} via ${upstream}; previous config saved as ${backup_file}"
}

configure_caddy

if command -v ufw >/dev/null 2>&1 && ufw status | grep -q '^Status: active'; then
  ufw allow 80/tcp >/dev/null
  ufw allow 443/tcp >/dev/null
  log 'Confirmed UFW allow rules for TCP 80/443; existing rules were preserved'
fi

if ((SKIP_CADDY == 0)); then
  local_http_code="$(curl --silent --output /dev/null --write-out '%{http_code}' --max-time 10 -H "Host: ${DOMAIN}" http://127.0.0.1/ || true)"
  case "${local_http_code}" in
    200|301|302|307|308)
      log "Local Caddy host-routing check returned HTTP ${local_http_code}"
      ;;
    *)
      warn "local Caddy host-routing check returned HTTP ${local_http_code:-none}"
      ;;
  esac

  if curl --fail --silent --show-error --head --max-time 30 "https://${DOMAIN}/" >/dev/null 2>&1; then
    log "Public HTTPS check passed: https://${DOMAIN}/"
  else
    if [[ "${CADDY_MODE}" == "container" ]]; then
      warn "public HTTPS is not ready yet. Confirm the Beget A record for ${DOMAIN}, then inspect: docker logs --tail 100 ${CADDY_CONTAINER}"
    else
      warn "public HTTPS is not ready yet. Confirm the Beget A record for ${DOMAIN}, then inspect: journalctl -u caddy -n 100 --no-pager"
    fi
  fi
fi

log 'Deployment completed successfully'
"${COMPOSE[@]}" ps --all
cat <<EOF

Site:          https://${DOMAIN}/
Application:   ${APP_DIR}
Environment:   ${ENV_FILE}
Deploy log:    ${LOG_FILE}
Database:      ${table_count} tables

Update command (same options may be reused):
  ${PROGRAM_NAME} --repo '${REPO_URL}' --ref '${GIT_REF}' --domain '${DOMAIN}' --port '${APP_PORT}' --project '${COMPOSE_PROJECT}'

Diagnostics:
EOF

printf '  '
printf '%q ' "${COMPOSE[@]}"
printf 'ps --all\n'
printf '  '
printf '%q ' "${COMPOSE[@]}"
printf 'logs --tail 100 web db\n'

if [[ "${CADDY_MODE}" == "container" ]]; then
  printf '  docker logs --tail 100 %q\n' "${CADDY_CONTAINER}"
elif [[ "${CADDY_MODE}" == "host" ]]; then
  printf '  journalctl -u caddy -n 100 --no-pager\n'
fi
