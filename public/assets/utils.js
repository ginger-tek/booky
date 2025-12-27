export function submitBusy() {
  document.querySelectorAll('form').forEach(frm => {
    frm.addEventListener('submit', () => {
      const btn = frm.querySelector('[type=submit]')
      btn.setAttribute('disabled', true)
      btn.setAttribute('aria-busy', true)
    })
  })
}

export function togglePasswordVisibility() {
  document.querySelectorAll('.toggle-password').forEach(toggle => {
    const input = toggle.nextElementSibling
    const show = () => {
      input.type = 'text'
      toggle.innerHTML = '<i class="bi bi-eye"></i>'
    }
    const hide = () => {
      input.type = 'password'
      toggle.innerHTML = '<i class="bi bi-eye-slash"></i>'
    }
    toggle.addEventListener('mousedown', show)
    toggle.addEventListener('mouseup', hide)
    toggle.addEventListener('touchstart', show)
    toggle.addEventListener('touchend', hide)
  })
}

export function ctrlSave(form) {
  form.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault()
      form.submit()
    }
  })
  setTimeout(() => form.querySelector('input, textarea, select').focus())
}

export function initFilterChildElements(container, searchInput, emptyText) {
  emptyText = emptyText || 'No results found';
  emptyText = `<div style="text-align:center">${emptyText}</div>`
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

export function printInvoice(invoiceId) {
  const printWindow = window.open(`/invoices/${invoiceId}/print`, `Invoice #${invoiceId}`);
  printWindow.onload = () => {
    setTimeout(() => {
      printWindow.print();
      printWindow.close();
    });
  };
}

export async function confirmAsync(message) {
  if (!message) throw new Error('Message is required');
  return new Promise((resolve) => {
    const dialog = document.createElement('dialog');
    dialog.innerHTML = `<article>
      <h3>Confirm</h3>
      <p>${message}</p>
      <menu>
        <button value="cancel">Cancel</button>
        <button value="confirm" autofocus>OK</button>
      </menu>
    </article>`
    dialog.addEventListener('close', () => {
      resolve(dialog.returnValue === 'confirm');
      setTimeout(() => document.body.removeChild(dialog), 300);
    });
  });
}

export async function startSessionTimer() {
  let warningTimer;
  let logoutTimer;
  const token = await cookieStore.get('token');
  if (!token) return;
  const expTime = parseInt(token.expires) * 1000;
  const warningTime = expTime - 30000; // 30 seconds before expiration
  console.log('Session expiration time:', new Date(expTime).toLocaleString());
  console.log('Session warning time:', new Date(warningTime).toLocaleString());
  const currentTime = Date.now();
  console.log(`Session time left: 00:${Math.floor((expTime - currentTime) / 1000 / 60)}:${Math.round((expTime - currentTime) / 1000 / 60 % 1 * 60).toString().padStart(2, '0')}`);
  if (currentTime >= expTime) {
    alert('Your session has expired. You will be logged out.');
    window.location.href = '/logout';
    return;
  }
  const timeUntilWarning = warningTime - currentTime;
  warningTimer = setTimeout(async () => {
    const extend = await confirmAsync('Your session is about to expire. Do you want to extend your session?');
    if (extend) {
      fetch('/refresh', {
        method: 'POST',
        credentials: 'include'
      })
        .then(() => location.reload())
        .catch(() => {
          alert('Failed to extend session. You will be logged out.');
          window.location.href = '/logout';
        });
    } else {
      alert('You will be logged out due to inactivity.');
      window.location.href = '/logout';
    }
  }, timeUntilWarning);
  const timeUntilLogout = expTime - currentTime;
  logoutTimer = setTimeout(() => {
    alert('Your session has expired. You will be logged out.');
    window.location.href = '/logout';
  }, timeUntilLogout);
}