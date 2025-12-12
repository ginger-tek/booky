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
    toggle.addEventListener('mousedown', () => {
      input.type = 'text'
      toggle.innerHTML = '<i class="bi bi-eye"></i>'
    })
    toggle.addEventListener('mouseup', () => {
      input.type = 'password'
      toggle.innerHTML = '<i class="bi bi-eye-slash"></i>'
    })
  })
}

export function ctrlSave(form) {
  form.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault()
      form.submit()
    }
  })
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