"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _createForOfIteratorHelper(o) { if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (o = _unsupportedIterableToArray(o))) { var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var it, normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(n); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { return function () { var Super = _getPrototypeOf(Derived), result; if (_isNativeReflectConstruct()) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

(function (element, blockEditor, blocksConfig) {
  var el = element.createElement;
  var __ = wp.i18n.__;
  var availableBlocks = blocksConfig.blockTypes;
  var SelectControl = wp.components.SelectControl;
  var knownBlocks = {
    'sociallocker': {
      'title': __('Social Locker', 'bizpanda'),
      'description': __('Hides content inside the block behind a social locker.', 'bizpanda'),
      'keywords': ['locker', 'sociallocker', 'social locker', 'social', 'lock'],
      'transformsFrom': ['bizpanda/signinlocker', 'bizpanda/emaillocker'],
      'icon': el("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        viewBox: "0 0 992.13 992.13"
      }, el("path", {
        d: "M282.61,687.13l-88.25-88.25c-71.66-71.66-71.66-188.26,0-259.93a183.77,183.77,0,0,1,259.94,0l18.26,18.28L490.83,339A183.7,183.7,0,0,1,792.44,534.37l39.15,39.14A235.39,235.39,0,0,0,472.56,286a235.49,235.49,0,0,0-314.72,16.48c-91.79,91.79-91.79,241.16,0,332.95l88.25,88.26Z",
        fill: "#555d66",
        stroke: "#555d66",
        "stroke-miterlimit": "10",
        "stroke-width": "0.25"
      }), el("polygon", {
        points: "472.56 950.13 319.13 796.7 355.64 760.18 472.56 877.08 730.82 618.82 767.33 655.33 472.56 950.13 472.56 950.13",
        fill: "#555d66",
        stroke: "#555d66",
        "stroke-miterlimit": "10",
        "stroke-width": "60"
      }))
    },
    'signinlocker': {
      'title': __('Sign-In Locker', 'bizpanda'),
      'description': __('Hides content inside the block behind a sign-in locker.', 'bizpanda'),
      'keywords': ['locker', 'signinlocker', 'signin locker', 'social', 'lock'],
      'transformsFrom': ['bizpanda/sociallocker', 'bizpanda/emaillocker'],
      'icon': el("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        viewBox: "0 0 992.13 992.13"
      }, el("polygon", {
        points: "482.6 950.55 329.17 797.14 365.69 760.62 482.59 877.52 740.87 619.26 777.38 655.78 482.6 950.55 482.6 950.55",
        fill: "#555d66",
        stroke: "#555d66",
        "stroke-miterlimit": "10",
        "stroke-width": "60"
      }), el("path", {
        d: "M849.54,583.63L886,547.1,781.71,442.77H626.84l0-35c0.42-4.13,5.11-18.29,8.89-29.67C648,341,666.63,285,666.64,226.54c0-123.93-74-207.21-184.07-207.21S298.63,102.61,298.63,226.54C298.63,285,317.2,341,329.5,378.13c3.77,11.38,8.48,25.54,8.83,29l0,35.59H183.49L79.15,547.1l177,177,36.52-36.53L152.21,547.1l52.68-52.69H390V407.17c0-10.7-4.31-23.72-11.46-45.3-11.25-33.94-28.27-85.24-28.26-135.34C350.26,130.59,401,71,482.57,71S615,130.59,615,226.52c0,50.11-17,101.4-28.3,135.35-7.17,21.57-11.5,34.6-11.5,45.31v87.24H760.32Z",
        fill: "#555d66",
        stroke: "#555d66",
        "stroke-miterlimit": "10",
        "stroke-width": "0.25"
      }))
    },
    'emaillocker': {
      'title': __('Email Locker', 'bizpanda'),
      'description': __('Hides content inside the block behind an email locker.', 'bizpanda'),
      'keywords': ['locker', 'emaillocker', 'email locker', 'optin', 'opt-in', 'lock'],
      'transformsFrom': ['bizpanda/sociallocker', 'bizpanda/signinlocker'],
      'icon': el("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        viewBox: "0 0 992.13 992.13"
      }, el("path", {
        d: "M231.37,686.2l-91.73-91.73,255.85,0H586.16v-139H842.06l-89.44,89.44,36.53,36.52L940.9,429.61,560.35,49.07,40.77,568.65,194.85,722.72Zm354.8-538.28L842,403.78l-255.88,0V147.93Zm-51.63,0V542.81l-394.89,0Z",
        fill: "#555d66",
        stroke: "#555d66",
        "stroke-miterlimit": "10",
        "stroke-width": "0.25"
      }), el("polygon", {
        points: "421.33 949.21 267.89 795.75 304.4 759.25 421.33 876.16 679.58 617.89 716.09 654.4 421.33 949.21 421.33 949.21",
        fill: "#555d66",
        stroke: "#555d66",
        "stroke-miterlimit": "10",
        "stroke-width": "60"
      }))
    }
  };
  /**
   * Locker Select
  */

  var LockerSelect = /*#__PURE__*/function (_wp$element$Component) {
    _inherits(LockerSelect, _wp$element$Component);

    var _super = _createSuper(LockerSelect);

    function LockerSelect() {
      var _this;

      _classCallCheck(this, LockerSelect);

      _this = _super.apply(this, arguments);
      _this.state = {
        isLoading: true,
        selectedId: _this.props.lockerId ? parseInt(_this.props.lockerId) : 0,
        options: []
      };
      _this.handleSelectChange = _this.handleSelectChange.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(LockerSelect, [{
      key: "componentDidMount",
      value: function componentDidMount() {
        var self = this;
        var request = jQuery.ajax(window.ajaxurl, {
          type: 'post',
          dataType: 'json',
          data: {
            action: 'get_opanda_lockers',
            shortcode: self.props.shortcode
          }
        });
        request.done(function (data) {
          var options = [];
          var hasSelected = false;
          var defaultLocker = false;

          var _iterator = _createForOfIteratorHelper(data),
              _step;

          try {
            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              var locker = _step.value;

              if (self.state.selectedId && self.state.selectedId === parseInt(locker.id)) {
                hasSelected = true;
              }

              var item = {
                label: locker.title,
                value: locker.id,
                shortcode: locker.shortcode
              };
              if (!defaultLocker) defaultLocker = item;
              options.push(item);
            }
          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }

          if (!hasSelected && defaultLocker) {
            console.log(defaultLocker);
            self.props.onChange(defaultLocker);
          }

          self.setState({
            isLoading: false,
            options: options,
            selectedId: hasSelected ? self.state.selectedId : defaultLocker.value
          });
        });
      }
    }, {
      key: "handleSelectChange",
      value: function handleSelectChange(lockerId) {
        var option = null;

        var _iterator2 = _createForOfIteratorHelper(this.state.options),
            _step2;

        try {
          for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
            var optionItem = _step2.value;

            if (parseInt(optionItem.value) === parseInt(lockerId)) {
              option = optionItem;
              this.setState({
                selectedId: lockerId ? parseInt(lockerId) : 0
              });
              break;
            }
          }
        } catch (err) {
          _iterator2.e(err);
        } finally {
          _iterator2.f();
        }

        this.props.onChange(option);
      }
    }, {
      key: "render",
      value: function render() {
        var self = this;
        var options = this.state.options;

        if (this.state.isLoading) {
          options = [{
            label: __('Loading...', 'bizpanda'),
            value: 0
          }];
        }

        var hasLockers = options.length > 0;

        if (!hasLockers) {
          options = [{
            label: __('[ - empty - ]', 'bizpanda'),
            value: 0
          }];
        }

        var hasEditableItem = !this.state.isLoading && hasLockers && this.state.selectedId ? true : false;
        var hasAddAbility = !this.state.isLoading;
        return el(React.Fragment, null, el(SelectControl, {
          className: "onp-locker-select-wrap",
          label: self.props.label + ':',
          onChange: this.handleSelectChange,
          value: self.props.lockerId,
          options: options
        }), (hasAddAbility || hasEditableItem) && el("div", null, "|"), hasEditableItem && el("a", {
          href: blocksConfig.urlEditUrl.replace('{0}', this.state.selectedId),
          target: "_blank",
          className: "button onp-button"
        }, __('Edit', 'bizpanda')), hasAddAbility && el("a", {
          href: blocksConfig.urlCreateNew,
          target: "_blank",
          className: "button onp-button"
        }, __('Add', 'bizpanda')));
      }
    }]);

    return LockerSelect;
  }(wp.element.Component);

  var _iterator3 = _createForOfIteratorHelper(availableBlocks),
      _step3;

  try {
    var _loop = function _loop() {
      var pluginBlockType = _step3.value;
      if (!knownBlocks[pluginBlockType]) return "continue";
      var shortcode = pluginBlockType;
      var blockName = 'bizpanda/' + pluginBlockType;
      var blockTitle = knownBlocks[pluginBlockType].title;
      var blockDescription = knownBlocks[pluginBlockType].description;
      var blockIcon = knownBlocks[pluginBlockType].icon;
      var blockKeywords = knownBlocks[pluginBlockType].keywords;
      var blockTransformsFrom = knownBlocks[pluginBlockType].transformsFrom;
      wp.blocks.registerBlockType(blockName, {
        title: blockTitle,
        description: blockDescription,
        icon: blockIcon,
        category: 'widgets',
        keywords: blockKeywords,
        attributes: {
          id: {
            type: 'number'
          }
        },
        transforms: {
          from: [{
            type: 'block',
            blocks: blockTransformsFrom,
            transform: function transform(attributes) {
              return wp.blocks.createBlock(blockName, _objectSpread({}, attributes));
            }
          }]
        },
        edit: function edit(props) {
          var elements = []; // if selected, shows the settings

          if (props.isSelected) {
            var onChange = function onChange(option) {
              console.log(option);
              props.setAttributes({
                id: option && option.value ? parseInt(option.value) : null
              });
            };

            var configWrap = el("div", {
              className: "onp-config-wrap"
            }, el(LockerSelect, {
              shortcode: shortcode,
              label: blockTitle,
              onChange: onChange,
              lockerId: props.attributes.id
            }));
            elements.push(configWrap);
          }

          var previewWrap = el("div", {
            className: "onp-preview-wrap"
          }, el("div", {
            className: "onp-top-bracket"
          }), el(blockEditor.InnerBlocks, null), el("div", {
            className: "onp-bottom-bracket"
          }));
          elements.push(previewWrap);
          return el("div", {
            className: "onp-locker"
          }, elements);
        },
        save: function save(props) {
          return el("div", {
            className: "onp-locker-block"
          }, el(blockEditor.InnerBlocks.Content, null));
        }
      });
    };

    for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
      var _ret = _loop();

      if (_ret === "continue") continue;
    }
  } catch (err) {
    _iterator3.e(err);
  } finally {
    _iterator3.f();
  }
})(window.wp.element, window.wp.blockEditor, window.__bizpanda_locker_blocks);
