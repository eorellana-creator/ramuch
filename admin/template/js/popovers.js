/* global coreui */

 

document.querySelectorAll('[data-coreui-toggle="popover"]').forEach(element => {
  // eslint-disable-next-line no-new
  new coreui.Popover(element)
})

