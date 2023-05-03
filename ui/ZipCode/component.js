(() => {
  // ../../mailform/mailer/ui/ZipCode/component.jsx
  Catpow.UI.ZipCode = (props) => {
    const { useCallback, useState, useRef, useEffect } = React;
    const { className = "cmf-ui-zipcode" } = props;
    const { HiddenValues } = Catpow.Components;
    const refs = [useRef(), useRef()];
    const [value, setValue] = useState(props.value || "-");
    const [isComposing, setIsComposing] = useState(false);
    const secs = value.split("-").slice(0, 2);
    const setSec = useCallback((i, val, isComposing2) => {
      if (!val.match(/^\d+$/)) {
        val = "";
      }
      if (val.length == 7) {
        setValue(val.substring(0, 3) + "-" + val.substring(3));
      } else {
        secs[i] = val;
        if (i == 0 && val.length > 2) {
          if (!isComposing2) {
            refs[1].current.focus();
          }
        }
        setValue(secs.join("-"));
      }
    }, []);
    useEffect(() => {
      if (void 0 === window.AjaxZip3) {
        return;
      }
      window.AjaxZip3.zip2addr(refs[0].current, refs[1].current, props.pref, props.addr);
    }, [value]);
    const input = useCallback((i) => /* @__PURE__ */ React.createElement(
      "input",
      {
        type: "text",
        size: ["3", "4"][i],
        className: className + "__sec" + i,
        onChange: (e) => {
          var val = e.target.value;
          setSec(i, e.target.value, isComposing);
        },
        onCompositionStart: (e) => {
          setIsComposing(true);
        },
        onCompositionEnd: (e) => {
          setIsComposing(false);
          setSec(i, e.target.value, isComposing);
        },
        ref: refs[i],
        value: secs[i]
      }
    ), [className, setSec, setIsComposing]);
    return /* @__PURE__ */ React.createElement("div", { className }, input(0), /* @__PURE__ */ React.createElement("span", { className: className + "__sep" }, "-"), input(1), value && value !== "-" && /* @__PURE__ */ React.createElement(
      HiddenValues,
      {
        name: props.name,
        value
      }
    ));
  };
})();
