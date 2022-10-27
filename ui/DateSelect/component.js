function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

Catpow.UI.DateSelect = function (props) {
  var _React = React,
      useState = _React.useState,
      useReducer = _React.useReducer,
      useMemo = _React.useMemo,
      useCallback = _React.useCallback,
      useEffect = _React.useEffect;
  var _props$className = props.className,
      className = _props$className === void 0 ? "cmf-ui-dateselect" : _props$className;
  var getDateValue = useCallback(function (dateObj) {
    return dateObj.getFullYear() + '-' + (dateObj.getMonth() + 1) + '-' + dateObj.getDate();
  }, []);
  var getDateObject = useCallback(function (dateValue, defaultValue) {
    var d = dateValue.match(/^(\d+)\-(\d+)\-(\d+)$/);

    if (d) {
      return new Date(d[1], d[2] - 1, d[3]);
    }

    return getRelativeDateTimeObject(dateValue, defaultValue);
  }, []);
  var getRelativeDateTimeObject = useCallback(function (dateTimeValue, defaultValue) {
    if (dateTimeValue === 'now') {
      return new Date();
    }

    var r = dateTimeValue.match(/^([+\-]\d+)\s+(year|week|month|day|hour|minute|second)s?/);

    if (r) {
      var d = new Date();
      var rv = parseInt(r[1]);

      switch (r[2]) {
        case 'year':
          d.setFullYear(d.getFullYear() + rv);
          break;

        case 'week':
          d.setDate(d.getDate() + rv * 7);
          break;

        case 'month':
          d.setMonth(d.getMonth() + rv);
          break;

        case 'day':
          d.setDate(d.getDate() + rv);
          break;

        case 'hour':
          d.setHours(d.getHours() + rv);
          break;

        case 'minute':
          d.setMinutes(d.getMinutes() + rv);
          break;

        case 'second':
          d.setSeconds(d.getSeconds() + rv);
          break;
      }

      return d;
    }

    if (defaultValue) {
      return defaultValue;
    }

    return false;
  }, []);
  var now = useMemo(function () {
    return getDateObject('now');
  });

  var _useReducer = useReducer(function (state, action) {
    switch (action.type) {
      case 'init':
        {
          state.min = getDateObject(props.min || '-80 year');
          state.max = getDateObject(props.max || '+1 year');
          state.minTime = state.min.getTime();
          state.maxTime = state.max.getTime();
          state.minYear = state.min.getFullYear();
          state.maxYear = state.max.getFullYear();
          state.minMonth = 1;
          state.maxMonth = 12;
          state.minDate = 1;
          state.maxDate = 31;
          action.value = props.value;
          return _objectSpread({}, state);
        }

      case 'update':
        {
          var d = action.value ? getDateObject(action.value) : new Date(action.year || state.year || now.getFullYear(), (action.month || state.month || now.getMonth() + 1) - 1, action.date || state.date || now.getDate());

          if (isNaN(d.getTime())) {
            state.value = state.year = state.month = state.date = undefined;
            return _objectSpread({}, state);
          }

          var t = d.getTime();

          if (t < state.minTime) {
            d.setTime(state.minTime);
          }

          if (t > state.maxTime) {
            d.setTime(state.maxTime);
          }

          state.value = getDateValue(d);
          state.year = d.getFullYear();
          state.month = d.getMonth() + 1;
          state.date = d.getDate();

          if (d.getFullYear() === state.minYear) {
            state.minMonth = state.min.getMonth() + 1;

            if (d.getMonth() === state.minMonth - 1) {
              state.minDate = state.min.getDate();
            } else {
              state.minDate = 1;
            }
          } else {
            state.minMonth = 1;
            state.minDate = 1;
          }

          if (d.getFullYear() === state.maxYear) {
            state.maxMonth = state.max.getMonth() + 1;

            if (d.getMonth() === state.maxMonth - 1) {
              state.maxDate = state.max.getDate();
            } else {
              state.maxDate = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
            }
          } else {
            state.maxMonth = 12;
            state.maxDate = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
          }

          return _objectSpread({}, state);
        }
    }

    return state;
  }, {}),
      _useReducer2 = _slicedToArray(_useReducer, 2),
      state = _useReducer2[0],
      dispatch = _useReducer2[1];

  useEffect(function () {
    return dispatch({
      type: 'init'
    });
  }, []);
  return /*#__PURE__*/React.createElement("div", {
    className: className
  }, /*#__PURE__*/React.createElement("div", {
    className: className + "__inputs"
  }, /*#__PURE__*/React.createElement(Catpow.SelectNumber, {
    label: "---",
    min: state.minYear,
    max: state.maxYear,
    value: state.year,
    onChange: function onChange(year) {
      dispatch({
        type: 'update',
        year: year
      });
    }
  }), /*#__PURE__*/React.createElement("span", {
    className: className + "__inputs-unit"
  }, "\u5E74"), /*#__PURE__*/React.createElement(Catpow.SelectNumber, {
    label: "---",
    min: state.minMonth,
    max: state.maxMonth,
    value: state.month,
    onChange: function onChange(month) {
      dispatch({
        type: 'update',
        month: month
      });
    }
  }), /*#__PURE__*/React.createElement("span", {
    className: className + "__inputs-unit"
  }, "\u6708"), /*#__PURE__*/React.createElement(Catpow.SelectNumber, {
    label: "---",
    min: state.minDate,
    max: state.maxDate,
    value: state.date,
    onChange: function onChange(date) {
      dispatch({
        type: 'update',
        date: date
      });
    }
  }), /*#__PURE__*/React.createElement("span", {
    className: className + "__inputs-unit"
  }, "\u65E5")), state.value && /*#__PURE__*/React.createElement(Catpow.Components.HiddenValues, {
    name: props.name,
    value: state.value
  }));
};
