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

  function isValidPhone(value) {
    var trimmed = value.trim();
    if (!/^\+?[0-9\s().-]+$/.test(trimmed)) return false;
    var digits = trimmed.replace(/\D/g, '');
    return digits.length >= 10 && digits.length <= 15;
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
  }

  function contactMethod(root) {
    return root.getAttribute('data-rm-contact-method') || '';
  }

  function contactError(root, value) {
    var method = contactMethod(root);
    if (method === 'email') {
      return isValidEmail(value) ? '' : 'Укажите корректный адрес электронной почты.';
    }
    if (method === 'phone') {
      return isValidPhone(value) ? '' : 'Укажите корректный номер телефона.';
    }
    if (method === 'telegram' || method === 'max') {
      var trimmed = value.trim();
      return trimmed.length >= 3 && !/[\r\n]/.test(trimmed)
        ? ''
        : 'Укажите имя пользователя или номер телефона.';
    }
    return isValidContact(value) ? '' : 'Укажите корректный телефон или адрес электронной почты.';
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
      var value = root.getAttribute('data-rm-estimate-context') || window.location.pathname;
      var method = contactMethod(root);
      contextInput.value = method ? value + '; contact=' + method : value;
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

    var invalidContactMessage = contactInput ? contactError(root, contactInput.value || '') : 'Укажите контакт для связи.';
    if (invalidContactMessage) {
      firstInvalid = setError(contactGroup, invalidContactMessage);
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

  function ensureSuccessDialog() {
    var existing = document.querySelector('[data-rm-estimate-success-dialog]');
    if (existing) return existing;

    var dialog = document.createElement('dialog');
    dialog.className = 'rm-estimate-success-dialog';
    dialog.setAttribute('data-rm-estimate-success-dialog', '');
    dialog.setAttribute('aria-labelledby', 'rm-estimate-success-title');
    dialog.innerHTML =
      '<div class="rm-estimate-success-dialog__inner">' +
        '<span class="rm-estimate-success-dialog__mark" aria-hidden="true">✓</span>' +
        '<h2 id="rm-estimate-success-title">Ваша заявка принята</h2>' +
        '<p data-rm-estimate-success-message></p>' +
        '<button type="button" data-rm-estimate-success-close>ОК</button>' +
      '</div>';
    dialog.querySelector('[data-rm-estimate-success-close]').addEventListener('click', function () {
      dialog.close();
    });
    document.body.appendChild(dialog);
    return dialog;
  }

  function showSuccessDialog(name) {
    var dialog = ensureSuccessDialog();
    var message = dialog.querySelector('[data-rm-estimate-success-message]');
    var normalizedName = (name || '').trim();
    message.textContent = normalizedName
      ? 'Спасибо, ' + normalizedName + '! Мы получили ваш запрос и свяжемся с вами для уточнения деталей.'
      : 'Спасибо! Мы получили ваш запрос и свяжемся с вами для уточнения деталей.';
    if (!dialog.open) dialog.showModal();
    dialog.querySelector('[data-rm-estimate-success-close]').focus();
  }

  function showSubmitError(form, message) {
    var status = form.querySelector('[data-rm-submit-status]');
    if (!status) {
      status = document.createElement('p');
      status.className = 'rm-estimate-submit-status';
      status.setAttribute('data-rm-submit-status', '');
      status.setAttribute('role', 'alert');
      form.appendChild(status);
    }
    status.textContent = message;
  }

  function submitEstimate(root, form, submit) {
    if (!submit || submit.disabled) return;

    var nameInput = control(field(root, 'NAME'));
    var customerName = nameInput ? nameInput.value : '';
    var originalText = submit.value || submit.textContent || 'Отправить запрос';
    var formData = new FormData(form);
    if (submit.name && !formData.has(submit.name)) {
      formData.append(submit.name, submit.value || originalText);
    }

    var previousStatus = form.querySelector('[data-rm-submit-status]');
    if (previousStatus) previousStatus.remove();
    submit.disabled = true;
    submit.setAttribute('aria-busy', 'true');
    if ('value' in submit) submit.value = 'Отправляем…';
    else submit.textContent = 'Отправляем…';

    var controller = typeof window.AbortController === 'function' ? new AbortController() : null;
    var timeout = window.setTimeout(function () {
      if (controller) controller.abort();
    }, 20000);

    window.fetch(form.action || window.location.href, {
      method: (form.method || 'POST').toUpperCase(),
      body: formData,
      credentials: 'same-origin',
      redirect: 'follow',
      signal: controller ? controller.signal : undefined
    }).then(function (response) {
      return response.text().then(function (html) {
        return { ok: response.ok, url: response.url, html: html };
      });
    }).then(function (result) {
      var responseDocument = new DOMParser().parseFromString(result.html, 'text/html');
      var success = /(?:[?&])formresult=addok(?:&|$)/.test(result.url) ||
        Boolean(responseDocument.querySelector('.form.contacts.success'));
      if (!result.ok || !success) throw new Error('server-validation');
      showSuccessDialog(customerName);
    }).catch(function (error) {
      var message = error && error.name === 'AbortError'
        ? 'Сервер отвечает слишком долго. Проверьте соединение и попробуйте ещё раз.'
        : 'Не удалось отправить запрос. Проверьте заполненные поля и попробуйте ещё раз.';
      showSubmitError(form, message);
    }).finally(function () {
      window.clearTimeout(timeout);
      if (!submit.isConnected) return;
      submit.disabled = false;
      submit.removeAttribute('aria-busy');
      if ('value' in submit) submit.value = originalText;
      else submit.textContent = originalText;
    });
  }

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    var root = form.closest(rootSelector);
    if (!root) return;

    if (!validate(root)) {
      event.preventDefault();
      event.stopImmediatePropagation();
      return;
    }

    if (typeof window.fetch !== 'function' || typeof window.FormData !== 'function') return;
    event.preventDefault();
    event.stopImmediatePropagation();
    submitEstimate(
      root,
      form,
      event.submitter || form.querySelector('input[type="submit"], button[type="submit"]')
    );
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
      initializeCommunicationSelector(root);
      setContext(root);
      labelControls(root);
    });
  }

  function initializeCommunicationSelector(root) {
    if (root.getAttribute('data-rm-communication-selector') !== 'true') return;
    var contactGroup = field(root, 'CONTACT');
    var input = control(contactGroup);
    var formGroup = contactGroup ? contactGroup.querySelector('.form-group') : null;
    if (!input || !formGroup || formGroup.querySelector('[data-rm-contact-methods]')) return;

    var label = contactGroup.querySelector('label span');
    if (label) {
      var required = label.querySelector('.required-star');
      label.textContent = 'Как вам удобно получить расчёт?';
      if (required) {
        label.appendChild(document.createTextNode(' '));
        label.appendChild(required);
      }
    }

    var methods = [
      {
        value: 'telegram', label: 'Telegram', placeholder: 'Имя пользователя или телефон',
        icon: '<svg class="rm-contact-methods__icon" viewBox="170 285 575 480" aria-hidden="true"><path d="M226.328 494.722C372.089 431.217 469.285 389.35 517.917 369.122C656.773 311.367 685.625 301.335 704.431 301.004C708.568 300.931 717.816 301.956 723.806 306.817C728.865 310.921 730.257 316.466 730.923 320.357C731.589 324.249 732.418 333.114 731.759 340.041C724.234 419.102 691.675 610.965 675.111 699.515C668.102 736.984 654.301 749.548 640.941 750.777C611.905 753.449 589.856 731.588 561.733 713.153C517.727 684.306 492.866 666.349 450.15 638.2C400.784 605.669 432.786 587.789 460.919 558.569C468.282 550.921 596.215 434.556 598.691 424C599.001 422.68 599.288 417.759 596.365 415.16C593.441 412.562 589.126 413.45 586.012 414.157C581.599 415.159 511.298 461.625 375.11 553.556C355.155 567.259 337.081 573.935 320.887 573.585C303.034 573.199 268.693 563.491 243.164 555.192C211.851 545.014 186.964 539.633 189.132 522.346C190.26 513.343 202.659 504.135 226.328 494.722Z" fill="currentColor"/></svg>'
      },
      {
        value: 'max', label: 'MAX', placeholder: 'Имя пользователя или телефон',
        icon: '<svg class="rm-contact-methods__icon" viewBox="0 0 100 100" aria-hidden="true"><path d="M50.757.262c27.536 0 49.129 22.335 49.129 49.885 0 27.55-22.279 49.343-48.865 49.343-9.435 0-14.007-1.328-21.371-6.543a1.27 1.27 0 0 0-1.629.193c-5.664 6.043-20.171 10.286-20.835 2.036 0-14.386-7.186-23.729-7.186-45.3C0 21.555 23.221.262 50.757.262Zm.772 24.55C38.464 24.126 28.264 33.197 26.014 47.383c-1.864 11.75 1.436 26.072 4.265 26.793 1.2.307 4.078-1.9 6.178-3.879a1.244 1.244 0 0 1 1.45-.15c3.272 2 6.972 3.5 11.05 3.715 13.414.7 25.3-9.8 26.007-23.215.7-13.414-10.021-25.142-23.435-25.842v.007Z" fill="currentColor"/></svg>'
      },
      {
        value: 'phone', label: 'Телефон', placeholder: 'Номер телефона',
        icon: '<svg class="rm-contact-methods__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.2 3.8 4.7 5.1c-.9.5-1.2 1.5-.9 2.5 1.8 5.6 6.1 9.9 11.7 11.7 1 .3 2-.1 2.5-.9l1.3-2.5-4.1-2-1.1 1.7a13.1 13.1 0 0 1-5.7-5.7l1.7-1.1-1.9-4.1Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
      },
      {
        value: 'email', label: 'Почта', placeholder: 'Электронная почта',
        icon: '<svg class="rm-contact-methods__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
      }
    ];
    var chooser = document.createElement('div');
    chooser.className = 'rm-contact-methods';
    chooser.setAttribute('data-rm-contact-methods', '');
    chooser.setAttribute('role', 'radiogroup');
    chooser.setAttribute('aria-label', 'Удобный способ связи');

    function selectMethod(method, button) {
      root.setAttribute('data-rm-contact-method', method.value);
      chooser.querySelectorAll('[role="radio"]').forEach(function (item) {
        var selected = item === button;
        item.setAttribute('aria-checked', selected ? 'true' : 'false');
        item.tabIndex = selected ? 0 : -1;
      });
      input.placeholder = method.placeholder;
      input.setAttribute('inputmode', method.value === 'phone' ? 'tel' : (method.value === 'email' ? 'email' : 'text'));
      clearError(contactGroup);
      setContext(root);
    }

    methods.forEach(function (method, index) {
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'rm-contact-methods__button rm-contact-methods__button--' + method.value;
      button.setAttribute('role', 'radio');
      button.setAttribute('aria-checked', index === 0 ? 'true' : 'false');
      button.tabIndex = index === 0 ? 0 : -1;
      button.innerHTML = method.icon + '<span>' + method.label + '</span>';
      button.addEventListener('click', function () { selectMethod(method, button); });
      chooser.appendChild(button);
    });

    var inputWrapper = formGroup.querySelector('.input');
    formGroup.insertBefore(chooser, inputWrapper || null);
    selectMethod(methods[0], chooser.firstElementChild);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initializeContexts(document);
    });
  } else {
    initializeContexts(document);
  }
}());
