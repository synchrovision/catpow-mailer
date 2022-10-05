Catpow.SelectNumber = function (props) {
  var _props$className = props.className,
      className = _props$className === void 0 ? 'cmf-component-selectnumber' : _props$className,
      _props$min = props.min,
      min = _props$min === void 0 ? 1 : _props$min,
      _props$max = props.max,
      max = _props$max === void 0 ? 10 : _props$max,
      label = props.label,
      _props$step = props.step,
      step = _props$step === void 0 ? 1 : _props$step,
      value = props.value,
      _onChange = props.onChange;
  var _React = React,
      useState = _React.useState,
      useMemo = _React.useMemo;
  var selections = useMemo(function () {
    var selections = [];

    for (var i = parseInt(min); i <= parseInt(max); i += parseInt(step)) {
      selections.push(i);
    }

    return selections;
  }, [min, max, step]);
  return /*#__PURE__*/React.createElement("select", {
    className: className,
    onChange: function onChange(e) {
      _onChange(e.currentTarget.value);
    }
  }, label && /*#__PURE__*/React.createElement("option", {
    selected: value === undefined
  }, label), selections.map(function (i) {
    return /*#__PURE__*/React.createElement("option", {
      value: i,
      selected: value === i,
      key: i
    }, i);
  }));
};
