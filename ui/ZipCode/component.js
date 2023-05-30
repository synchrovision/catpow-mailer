(() => {
  // ../../mailform/mailer/ui/ZipCode/component.jsx
  Catpow.UI.ZipCode = (props) => {
    const { useCallback, useMemo, useState, useRef, useEffect } = React;
    const { className = "cmf-ui-zipcode" } = props;
    const { HiddenValues } = Catpow.Components;
    const ref0 = useRef();
    const ref1 = useRef();
    const refs = useMemo(() => [ref0, ref1], [ref0, ref1]);
    const [value, setValue] = useState(props.value || "-");
    const [isComposing, setIsComposing] = useState(false);
    const secs = value.split("-").slice(0, 2);
    const setSec = useCallback((i, val) => {
      if (!val.match(/^[\d\-]+$/)) {
        val = "";
      }
      const matches = val.match(/^(\d{3})\-?(\d{4})$/);
      if (matches) {
        secs[0] = matches[1];
        secs[1] = matches[2];
      } else {
        secs[i] = val;
        if (i == 0 && val.length > 2) {
          refs[1].current.focus();
        }
      }
      setValue(secs.join("-"));
    }, [refs]);
    useEffect(() => {
      if (void 0 === window.AjaxZip3) {
        return;
      }
      window.AjaxZip3.zip2addr(refs[0].current, refs[1].current, props.pref, props.addr);
    }, [refs, value]);
    const Input = useCallback((props2) => {
      const { className: className2, index, refs: refs2 } = props2;
      const [value2, setValue2] = useState(props2.value);
      const [isComposing2, setIsComposing2] = useState(false);
      useEffect(() => {
        if (!isComposing2) {
          setSec(index, value2);
        }
      }, [isComposing2, index, value2]);
      useEffect(() => {
        setValue2(props2.value);
      }, [props2.value]);
      return /* @__PURE__ */ React.createElement(
        "input",
        {
          type: "text",
          size: ["3", "4"][index],
          className: className2,
          onChange: (e) => {
            setValue2(e.target.value);
          },
          onCompositionStart: (e) => {
            setIsComposing2(true);
          },
          onCompositionEnd: (e) => {
            setIsComposing2(false);
            setValue2(e.target.value);
          },
          ref: refs2[index],
          value: value2
        }
      );
    }, [setSec]);
    return /* @__PURE__ */ React.createElement("div", { className }, /* @__PURE__ */ React.createElement(Input, { className: className + "__sec0", index: 0, value: secs[0], refs }), /* @__PURE__ */ React.createElement("span", { className: className + "__sep" }, "-"), /* @__PURE__ */ React.createElement(Input, { className: className + "__sec1", index: 1, value: secs[1], refs }), value && value !== "-" && /* @__PURE__ */ React.createElement(
      HiddenValues,
      {
        name: props.name,
        value
      }
    ));
  };
})();
