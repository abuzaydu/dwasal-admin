  /* Default Notifications */
  function default_noti(message) {
	Lobibox.notify('default', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		msg: message
	});
}

function info_noti(message) {
	Lobibox.notify('info', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		icon: 'fa fa-info-circle',
		msg: message
	});
}

function warning_noti(message) {
	Lobibox.notify('warning', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		icon: 'fa fa-warning',
		msg: message
	});
}

function error_noti(message) {
	Lobibox.notify('error', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		icon: 'fa fa-exclamation-triangle',
		msg: message
	});
}

function success_noti(message) {
	Lobibox.notify('success', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		icon: 'fa fa-check-circle',
		msg: message
	});
}
/* Rounded corners Notifications */
function round_default_noti(message) {
	Lobibox.notify('default', {
		pauseDelayOnHover: true,
		size: 'mini',
		rounded: true,
		delayIndicator: false,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		msg: message
	});
}

function round_info_noti(message) {
	Lobibox.notify('info', {
		pauseDelayOnHover: true,
		size: 'mini',
		rounded: true,
		icon: 'fa fa-info-circle',
		delayIndicator: false,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		msg: message
	});
}

function round_warning_noti(message) {
	Lobibox.notify('warning', {
		pauseDelayOnHover: true,
		size: 'mini',
		rounded: true,
		delayIndicator: false,
		icon: 'fa fa-warning',
		continueDelayOnInactiveTab: false,
		position: 'top right',
		msg: message
	});
}

function round_error_noti(message) {
	Lobibox.notify('error', {
		pauseDelayOnHover: true,
		size: 'mini',
		rounded: true,
		delayIndicator: false,
		icon: 'fa fa-exclamation-triangle',
		continueDelayOnInactiveTab: false,
		position: 'top right',
		msg: message
	});
}

function round_success_noti(message) {
	Lobibox.notify('success', {
		pauseDelayOnHover: true,
		size: 'mini',
		rounded: true,
		icon: 'fa fa-check-circle',
		delayIndicator: false,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		msg: message
	});
}
/* Notifications With Images*/
function img_default_noti(message) {
	Lobibox.notify('default', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		img: 'assets/plugins/notifications/img/1.jpg', //path to image
		msg: message
	});
}

function img_info_noti(message) {
	Lobibox.notify('info', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		icon: 'fa fa-info-circle',
		position: 'top right',
		img: 'assets/plugins/notifications/img/2.jpg', //path to image
		msg: message
	});
}

function img_warning_noti(message) {
	Lobibox.notify('warning', {
		pauseDelayOnHover: true,
		icon: 'fa fa-error',
		continueDelayOnInactiveTab: false,
		position: 'top right',
		img: 'assets/plugins/notifications/img/3.jpg', //path to image
		msg: message
	});
}

function img_error_noti(message) {
	Lobibox.notify('error', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		icon: 'fa fa-x-circle',
		position: 'top right',
		img: 'assets/plugins/notifications/img/4.jpg', //path to image
		msg: message
	});
}

function img_success_noti(message) {
	Lobibox.notify('success', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'top right',
		icon: 'fa fa-check-circle',
		img: 'assets/plugins/notifications/img/5.jpg', //path to image
		msg: message
	});
}
/* Notifications With Images*/
function pos1_default_noti(message) {
	Lobibox.notify('default', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'center top',
		size: 'mini',
		msg: message
	});
}

function pos2_info_noti(message) {
	Lobibox.notify('info', {
		pauseDelayOnHover: true,
		icon: 'fa fa-info-circle',
		continueDelayOnInactiveTab: false,
		position: 'top left',
		size: 'mini',
		msg: message
	});
}

function pos3_warning_noti(message) {
	Lobibox.notify('warning', {
		pauseDelayOnHover: true,
		icon: 'fa fa-error',
		continueDelayOnInactiveTab: false,
		position: 'top right',
		size: 'mini',
		msg: message
	});
}

function pos4_error_noti(message) {
	Lobibox.notify('error', {
		pauseDelayOnHover: true,
		icon: 'fa fa-x-circle',
		size: 'mini',
		continueDelayOnInactiveTab: false,
		position: 'bottom left',
		msg: message
	});
}

function pos5_success_noti(message) {
	Lobibox.notify('success', {
		pauseDelayOnHover: true,
		size: 'mini',
		icon: 'fa fa-check-circle',
		continueDelayOnInactiveTab: false,
		position: 'bottom right',
		msg: message
	});
}
/* Animated Notifications*/
function anim1_noti(message) {
	Lobibox.notify('default', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'center top',
		showClass: 'fadeInDown',
		hideClass: 'fadeOutDown',
		width: 600,
		msg: message
	});
}

function anim2_noti(message) {
	Lobibox.notify('info', {
		pauseDelayOnHover: true,
		icon: 'fa fa-info-circle',
		continueDelayOnInactiveTab: false,
		position: 'center top',
		showClass: 'bounceIn',
		hideClass: 'bounceOut',
		width: 600,
		msg: message
	});
}

function anim3_noti(message) {
	Lobibox.notify('warning', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		icon: 'fa fa-error',
		position: 'center top',
		showClass: 'zoomIn',
		hideClass: 'zoomOut',
		width: 600,
		msg: message
	});
}

function anim4_noti(message) {
	Lobibox.notify('error', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		icon: '',
		position: 'center top',
		showClass: 'lightSpeedIn',
		hideClass: 'lightSpeedOut',
		icon: 'fa fa-x-circle',
		width: 600,
		msg: message
	});
}

function anim5_noti(message) {
	Lobibox.notify('success', {
		pauseDelayOnHover: true,
		continueDelayOnInactiveTab: false,
		position: 'center top',
		showClass: 'rollIn',
		hideClass: 'rollOut',
		icon: 'fa fa-check-circle',
		width: 600,
		msg: message
	});
}