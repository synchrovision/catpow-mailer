function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

if (!("AjaxZip3" in window)) {
  Catpow.MailForm.loadScript('https://ajaxzip3.github.io/ajaxzip3.js');
}

Catpow.UI.ZipCode = function (props) {
  var _React = React,
      useCallback = _React.useCallback,
      useState = _React.useState,
      useRef = _React.useRef,
      useEffect = _React.useEffect;
  var HiddenValues = Catpow.Components.HiddenValues;
  var refs = [useRef(), useRef()];

  var _useState = useState(props.value || '-'),
      _useState2 = _slicedToArray(_useState, 2),
      value = _useState2[0],
      setValue = _useState2[1];

  var _useState3 = useState(false),
      _useState4 = _slicedToArray(_useState3, 2),
      isComposing = _useState4[0],
      setIsComposing = _useState4[1];

  var secs = value.split('-').slice(0, 2);
  var setSec = useCallback(function (i, val, isComposing) {
    if (!val.match(/^\d+$/)) {
      val = '';
    }

    if (val.length == 7) {
      setValue(val.substring(0, 3) + '-' + val.substring(3));
    } else {
      secs[i] = val;

      if (i == 0 && val.length > 2) {
        if (!isComposing) {
          refs[1].current.focus();
        }
      }

      setValue(secs.join('-'));
    }
  }, []);
  useEffect(function () {
    AjaxZip3.zip2addr(refs[0].current, refs[1].current, props.pref, props.addr);
  }, [value]);
  var input = useCallback(function (i) {
    return /*#__PURE__*/React.createElement("input", {
      type: "text",
      size: ["3", "4"][i],
      className: "sec" + i,
      onChange: function onChange(e) {
        var val = e.target.value;
        setSec(i, e.target.value, isComposing);
      },
      onCompositionStart: function onCompositionStart(e) {
        setIsComposing(true);
      },
      onCompositionEnd: function onCompositionEnd(e) {
        setIsComposing(false);
        setSec(i, e.target.value, isComposing);
      },
      ref: refs[i],
      value: secs[i]
    });
  });
  return /*#__PURE__*/React.createElement("div", {
    className: 'ZipCode'
  }, input(0), /*#__PURE__*/React.createElement("span", {
    class: "sep"
  }, "-"), input(1), /*#__PURE__*/React.createElement(HiddenValues, {
    name: props.name,
    value: value
  }));
};
