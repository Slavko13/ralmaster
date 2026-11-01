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

    var methods = [
      { value: 'telegram', label: 'Telegram', placeholder: 'Имя пользователя или телефон' },
      { value: 'max', label: 'MAX', placeholder: 'Имя пользователя или телефон' },
      { value: 'email', label: 'Почта', placeholder: 'Электронная почта' },
      { value: 'phone', label: 'Телефон', placeholder: 'Номер телефона' }
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
      button.textContent = method.label;
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
