/**
 * cbpFWTabs.js v1.0.0 (modified)
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */

;(function(window) {

	'use strict';

	function extend(a, b) {
		for (var key in b) {
			if (b.hasOwnProperty(key)) {
				a[key] = b[key];
			}
		}
		return a;
	}

	function PSTabs(el, options) {
		this.el = el;
		this.options = extend({}, this.options);
  		extend(this.options, options);
  		this._init();
	}

	PSTabs.prototype.options = {
		start : 0
	};

	PSTabs.prototype._init = function() {
		// get current index
		this.index = Number(document.URL.substring(document.URL.indexOf("#stripe_step_") + 13));
		// tabs elems
		this.tabs = [].slice.call(this.el.querySelectorAll('nav > a'));
		// content items
		this.items = [].slice.call(this.el.querySelectorAll('.content-wrap > section'));
		// set current
		this.current = -1;
		// current index
		this.options.start = (this.index != NaN ? Number(this.index) - 1 : 0);
		// show current content item
		this._show();
		// init events
		this._initEvents();
	};

	PSTabs.prototype._initEvents = function() {
		var self = this;
		this.tabs.forEach(function(tab, idx) {
			tab.addEventListener('click', function(ev) {
				self._show(idx);
			});
		});
	};

	PSTabs.prototype._show = function(idx) {
		if (this.current >= 0) {
			this.tabs[ this.current ].className = 'list-group-item';
			this.items[ this.current ].className = '';
		}
		// change current
		this.current = idx != undefined ? idx : this.options.start >= 0 && this.options.start < this.items.length ? this.options.start : 0;
		this.tabs[ this.current ].className = 'list-group-item tab-current active';
		this.items[ this.current ].className = 'content-current';
	};

	// add to global namespace
	window.PSTabs = PSTabs;

	var div_3d = '- ' + Translation[3]+' </br>- '+Translation[0]+' </br>- '+Translation[1]+' </br>- '+Translation[2]+'<a href="https://support.stripe.com/questions/does-stripe-support-3d-secure-verified-by-visa-mastercard-securecode" target="_blank">https://support.stripe.com/questions/does-stripe-support-3d-secure-verified-by-visa-mastercard-securecode</a></div>';

	$('#section-shape-4 .panel-heading').after('<div class="commit_3d"> '+ div_3d);
	$('#section-shape-4 legend').after('<div class="margin-form commit_3d_15"> '+div_3d);
})(window);
