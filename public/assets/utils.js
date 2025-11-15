export function sumbmitBusy() {
  document.querySelectorAll('form').forEach(frm => {
    frm.addEventListener('submit', () => {
      const btn = frm.querySelector('[type=submit]')
      btn.setAttribute('disabled', true)
      btn.setAttribute('aria-busy', true)
    })
  })
}
