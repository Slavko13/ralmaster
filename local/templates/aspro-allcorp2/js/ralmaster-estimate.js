(function () {
  'use strict';

  var rootSelector = '[data-rm-estimate-form]';
  var allowedExtensions = [
    'pdf', 'png', 'jpg', 'jpeg', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'dwg', 'dxf'
  ];
  var maxFileSize = 20 * 1024 * 1024;

  function field(root, sid) {
    return root.querySelector('[data-SID="' + sid + '"]');
  }

  function control(group) {
    if (!group) return null;
    if (group.matches && group.matches('input, textarea, select')) return group;
    return group.querySelector('input, textarea, select');
  }

  function clearError(group) {
    if (!group) return;

    group.classList.remove('rm-estimate-field--invalid');
    var input = control(group);
    var error = group.querySelector('.rm-estimate-error');
    if (input) {
      input.removeAttribute('aria-invalid');
      if (error && input.getAttribute('aria-describedby') === error.id) {
        input.removeAttribute('aria-describedby');
      }
    }
    if (error) error.remove();
  }

  function setError(group, message) {
    if (!group) return null;

    clearError(group);
    var input = control(group);
    var error = document.createElement('p');
    error.className = 'rm-estimate-error';
    error.id = 'rm-estimate-error-' + Math.random().toString(36).slice(2, 10);
    error.textContent = message;
    error.setAttribute('role', 'alert');
    group.appendChild(error);
    group.classList.add('rm-estimate-field--invalid');
    if (input) {
      input.setAttribute('aria-invalid', 'true');
      input.setAttribute('aria-describedby', error.id);
    }
    return input;
  }

  function isValidContact(value) {
    var trimmed = value.trim();
    if (!trimmed) return false;
    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed)) return true;
    if (!/^\+?[0-9\s().-]+$/.test(trimmed)) return false;

    var digits = trimmed.replace(/\D/g, '');
    return digits.length >= 10 && digits.length <= 15;
  }

  function fileError(fileInput) {
    if (!fileInput || !fileInput.files || !fileInput.files.length) return '';
    if (fileInput.files.length > 1) return 'Прикрепите один файл за одну отправку формы.';

    var file = fileInput.files[0];
    if (file.size > maxFileSize) return 'Размер файла превышает допустимый лимит 20 МБ.';

    var parts = file.name.toLowerCase().split('.');
    var extension = parts.length > 1 ? parts.pop() : '';
    if (allowedExtensions.indexOf(extension) === -1) {
      return 'Разрешены PDF, изображения, Office, DWG и DXF.';
    }

    return '';
  }

  function setContext(root) {
    var contextGroup = field(root, 'CONTEXT');
    var contextInput = control(contextGroup);
    if (contextInput) {
      contextInput.value = root.getAttribute('data-rm-estimate-context') || window.location.pathname;
    }
  }

  function labelControls(root) {
    root.querySelectorAll('[data-SID]').forEach(function (group) {
      var input = group.matches('input, textarea, select')
        ? group
        : group.querySelector('input:not([type="hidden"]), textarea, select');
      if (!input || input.getAttribute('aria-label') || input.getAttribute('aria-labelledby')) return;

      var label = group.querySelector('label');
      var text = label
        ? label.textContent.replace(/\s+/g, ' ').replace(/\s*[:*]\s*[:*]?\s*$/, '').trim()
        : '';
      var fallback = input.type === 'file'
        ? 'Прикрепить файл'
        : (input.placeholder || group.getAttribute('data-SID') || 'Поле формы');
      input.setAttribute('aria-label', text || fallback);
    });
  }

  function validate(root) {
    var contactGroup = field(root, 'CONTACT');
    var taskGroup = field(root, 'TASK');
    var fileGroup = field(root, 'FILE');
    var contactInput = control(contactGroup);
    var taskInput = control(taskGroup);
    var fileInput = fileGroup ? fileGroup.querySelector('input[type="file"]') : null;
    var firstInvalid = null;

    [contactGroup, taskGroup, fileGroup].forEach(clearError);
    setContext(root);

    if (!contactInput || !isValidContact(contactInput.value || '')) {
      firstInvalid = setError(contactGroup, 'Укажите корректный телефон или адрес электронной почты.');
    }

    var hasTask = Boolean(taskInput && taskInput.value.trim());
    var hasFile = Boolean(fileInput && fileInput.files && fileInput.files.length);
    if (!hasTask && !hasFile) {
      var taskInvalid = setError(taskGroup, 'Опишите задачу или прикрепите файл.');
      firstInvalid = firstInvalid || taskInvalid;
    }

    var uploadError = fileError(fileInput);
    if (uploadError) {
      var fileInvalid = setError(fileGroup, uploadError);
      firstInvalid = firstInvalid || fileInvalid;
    }

    if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus();
    return !firstInvalid;
  }

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    var root = form.closest(rootSelector);
    if (!root) return;

    if (!validate(root)) {
      event.preventDefault();
      event.stopImmediatePropagation();
    }
  }, true);

  document.addEventListener('input', function (event) {
    var target = event.target;
    if (!(target instanceof Element)) return;
    var root = target.closest(rootSelector);
    if (!root) return;
    var group = target.closest('[data-SID]');
    if (group) clearError(group);
  });

  document.addEventListener('change', function (event) {
    var target = event.target;
    if (!(target instanceof HTMLInputElement) || target.type !== 'file') return;
    var root = target.closest(rootSelector);
    if (!root) return;

    var group = target.closest('[data-SID="FILE"]');
    clearError(group);
    var message = fileError(target);
    if (message) setError(group, message);
  });

  function initializeContexts(scope) {
    scope.querySelectorAll(rootSelector).forEach(function (root) {
      setContext(root);
      labelControls(root);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initializeContexts(document);
    });
  } else {
    initializeContexts(document);
  }
}());
