var docEl = document.documentElement,
	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function() {
		if(docEl.clientWidth < 640 && docEl.clientWidth > 320) {
			docEl.style.fontSize = 10 * (docEl.clientWidth / 320) + 'px';
		} else if(docEl.clientWidth >= 640) {
			docEl.style.fontSize = '20px';
		} else if(docEl.clientWidth <= 320) {
			docEl.style.fontSize = '10px';
		}
	};
window.addEventListener(resizeEvt, recalc, false);
document.addEventListener('DOMContentLoaded', recalc, false);