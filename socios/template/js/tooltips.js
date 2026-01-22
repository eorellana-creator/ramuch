  

document.querySelectorAll('[data-coreui-toggle="tooltip"]').forEach(element => {
  // eslint-disable-next-line no-new
  new coreui.Tooltip(element)
})
