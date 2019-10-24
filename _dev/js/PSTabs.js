/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author   MIT license
 * @copyright Copyright 2014, Codrops
 * @license   Commercial license
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
})(window);
