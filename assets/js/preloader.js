/* Hides the Bungry preloader once the page has loaded (or after 4 seconds at most). */
(function () {
	var el = document.querySelector('.bungry-preloader');
	if (!el) {
		return;
	}
	var done = function () {
		el.classList.add('is-done');
		el.setAttribute('aria-hidden', 'true');
	};
	if (document.readyState === 'complete') {
		done();
	} else {
		window.addEventListener('load', done);
	}
	setTimeout(done, 4000);
})();
