Catpow.Components.HiddenValues = function (props) {
  var _React = React,
      useCallback = _React.useCallback;
  var hiddenInput = useCallback(function (name, val) {
    if (val instanceof Object) {
      return Object.keys(val).map(function (k) {
        return hiddenInput(name + '[' + k + ']', val[k]);
      });
    } else {
      return /*#__PURE__*/React.createElement("input", {
        type: "hidden",
        name: name,
        value: val
      });
    }
  }, [props]);
  return /*#__PURE__*/React.createElement("div", {
    className: 'hiddenValues'
  }, hiddenInput(props.name, props.value));
};
