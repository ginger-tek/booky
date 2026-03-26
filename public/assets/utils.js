export const configFormSubmit = (form, opts = {}) => {
  if (typeof form === 'string')
    form = document.querySelector(form);
  if (opts.ctrlSave === true)
    ctrlSave(form);
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (opts.confirm) {
      const confirmed = await confirmAsync(opts.confirm.title, opts.confirm.message, ...opts.confirm.yes);
      if (!confirmed) return;
    }
    const btn = opts.findSubmit && opts.findSubmit instanceof Function ? opts.findSubmit(form) : form.querySelector('[type=submit]');
    setBusy(btn, true);
    const formData = new FormData(form);
    const res = await fetch(form.action || opts.action, {
      method: form.method,
      body: formData,
      headers: { 'Accept': 'application/json' },
      credentials: 'include'
    });
    if (!res.ok) {
      const errorData = await res.json();
      alert(errorData.message || 'An error occurred');
      setBusy(btn, false);
      return;
    }
    if (res.redirected) {
      window.location.href = res.url;
      return;
    }
    if (opts.asyncCallback && opts.asyncCallback instanceof Function) {
      const data = await res.json();
      opts.asyncCallback(data);
    }
    setBusy(btn, false);
  })
}

export const setBusy = (btn, isBusy = true) => {
  if (isBusy === true) {
    btn.setAttribute('disabled', true);
    btn.setAttribute('aria-busy', true);
  } else {
    btn.removeAttribute('disabled');
    btn.removeAttribute('aria-busy');
  }
}

export const togglePasswordVisibility = () => {
  document.querySelectorAll('.toggle-password').forEach(toggle => {
    const input = toggle.nextElementSibling;
    const show = () => {
      input.type = 'text';
      toggle.innerHTML = '<i class="bi bi-eye"></i>';
    }
    const hide = () => {
      input.type = 'password';
      toggle.innerHTML = '<i class="bi bi-eye-slash"></i>';
    }
    toggle.addEventListener('mousedown', show);
    toggle.addEventListener('mouseup', hide);
    toggle.addEventListener('touchstart', show);
    toggle.addEventListener('touchend', hide);
  })
}

var fi;
export const ctrlSave = (form) => {
  form.addEventListener('keydown', async (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault();
      form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
  })
  fi ??= form.querySelector(':is(input, textarea, select):not(:disabled)');
  setTimeout(() => fi?.focus());
}

export const initFilterChildElements = (container, searchInput, emptyText) => {
  emptyText = emptyText || 'No results found';
  emptyText = `<div style="text-align:center">${emptyText}</div>`;
  const isTable = container.tagName === 'TABLE' || container.tagName === 'TBODY';
  const show = isTable ? 'table-row' : 'block';
  const noResults = document.createElement(isTable ? 'tr' : 'div');
  noResults.innerHTML = isTable ? `<td colspan="${container.children[0].children.length}">${emptyText}</td>` : emptyText;
  noResults.style.display = 'none';
  noResults.style.textAlign = 'center';
  if (isTable) container.appendChild(noResults);
  else container.parentNode.insertBefore(noResults, container.nextSibling);
  searchInput.oninput = (e) => {
    const query = e.target.value.toLowerCase();
    [...container.children].forEach(c => {
      const text = c.innerText.toLowerCase();
      c.style.display = text.includes(query) ? show : 'none';
    });
    noResults.style.display = [...container.children].every(c => c.style.display === 'none') ? show : 'none';
  }
}

export const printInvoice = (invoiceId, templateId) => {
  const printWindow = window.open(`/invoices/${invoiceId}/print${templateId ? `?templateId=${templateId}` : ''}`, `Invoice #${invoiceId}`);
  printWindow.onload = () => {
    setTimeout(() => {
      printWindow.print();
      printWindow.close();
    });
  };
}

export const confirmAsync = async (title, message, btnTexts = { yes: 'Yes', no: 'No' }) => {
  if (!message) throw new Error('Message is required');
  const dialog = document.createElement('dialog');
  document.body.appendChild(dialog);
  dialog.innerHTML = `<article>
    <h4>${title}</h4>
    <p>${message}</p>
    <div class="flex fill">
      <button class="secondary" onclick="this.closest('dialog').close('false')">${btnTexts.no}</button>
      <button onclick="this.closest('dialog').close('true')">${btnTexts.yes}</button>
    </div>
  </article>`
  dialog.showModal();
  return new Promise((resolve) => {
    dialog.addEventListener('close', () => {
      setTimeout(() => {
        dialog.remove();
        resolve(dialog.returnValue === 'true');
      }, 200);
    });
  });
}

export const startSessionTimer = async () => {
  const token = await cookieStore.get('token');
  if (!token) return;
  const expTime = parseInt(token.expires);
  const buffer = 30; // seconds
  const warningTime = expTime - (buffer * 1000);
  console.log('Session expiration time:', new Date(expTime).toLocaleString());
  console.log('Session warning time:', new Date(warningTime).toLocaleString());
  const currentTime = Date.now();
  console.log(`Session time left: 00:${Math.floor((expTime - currentTime) / 1000 / 60)}:${Math.round((expTime - currentTime) / 1000 / 60 % 1 * 60).toString().padStart(2, '0')}`);
  const timeUntilWarning = warningTime - currentTime;
  setTimeout(async () => {
    setTimeout(() => {
      console.log('Logging out due to session expiration');
      window.location.href = '/logout';
    }, buffer * 1000);
    const extend = await confirmAsync('Confirmation Needed', `Your session is about to expire in ${buffer} seconds and you will be logged out. Do you want to stay logged in and extend your session or log out now?`, { yes: 'Stay Logged In', no: 'Logout Now' });
    if (extend) {
      const res = await fetch('/refresh', {
        method: 'POST',
        credentials: 'include'
      });
      res.ok
        ? console.log('Session extended')
        : window.location.href = '/logout';
    } else
      window.location.href = '/logout';
  }, timeUntilWarning);
}