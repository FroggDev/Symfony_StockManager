/**
 * Init main JS container
 */
document.app={};

/**
 * Init Materialize elements
 */
document.app.tooltips   = M.Tooltip.init(document.querySelectorAll('.tooltipped'));
document.app.parallax   = M.Parallax.init(document.querySelectorAll('.parallax'));
document.app.sidenav    = M.Sidenav.init(document.querySelector('.sidenav'),{ edge:'right'});
document.app.loader     = M.Modal.init(document.querySelector('#modal-loader'));
document.app.dropdowns  = M.Dropdown.init(document.querySelectorAll('.dropdown-trigger'));
