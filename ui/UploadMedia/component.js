(() => {
  // modules/util/bem.jsx
  var bem = (className) => {
    const children = {};
    return new Proxy(function() {
      if (arguments.length > 0) {
        const classes = [];
        let i;
        for (i = 0; i < arguments.length; i++) {
          if (typeof arguments[i] === "string") {
            classes.push(arguments[i]);
            continue;
          }
          classes.push.apply(
            classes,
            Array.isArray(arguments[i]) ? arguments[i] : Object.keys(arguments[i]).filter((c) => arguments[i][c])
          );
        }
        if (classes.length > 0) {
          return className + " " + classes.join(" ");
        }
      }
      return className;
    }, {
      get: (target, prop) => {
        if (void 0 === children[prop]) {
          children[prop] = bem(className.split(" ")[0] + (prop[0] === "_" ? "_" : "-") + prop);
        }
        return children[prop];
      }
    });
  };

  // modules/util/calc.jsx
  var dataSizeStringToInt = (sizeString) => {
    const matches = sizeString.match(/(\d[\d,]*(?:\.\d+)?)([KMG])B/i);
    if (matches) {
      return parseInt(matches[1] * { "K": 2 << 10, "M": 2 << 20, "G": 2 << 30 }[matches[2]]);
    }
    return parseInt(sizeString);
  };
  var intToDataSizeString = (sizeInt) => {
    let grade = 0;
    while (sizeInt > 1e3) {
      sizeInt /= 1024;
      grade++;
    }
    return Math.round(sizeInt * 10) / 10 + ["byte", "KB", "MB", "GB", "TB", "PB"][grade];
  };

  // modules/component/Portal.jsx
  var Portal = (props) => {
    const { children, trace } = props;
    const { render, useState, useMemo: useMemo2, useCallback, useEffect, useRef } = React;
    const { createPortal } = ReactDOM;
    const ref = useRef({ contents: false, setContents: () => {
    } });
    const el = useMemo2(() => {
      if (props.id) {
        const exEl = document.getElementById(props.id);
        if (exEl) {
          return exEl;
        }
      }
      const el2 = document.createElement("div");
      if (props.id) {
        el2.id = props.id;
      }
      el2.className = props.className;
      document.body.appendChild(el2);
      return el2;
    }, []);
    useEffect(() => {
      const { trace: trace2 } = props;
      if (!trace2) {
        return;
      }
      el.style.position = "absolute";
      const timer = setInterval(() => {
        if (trace2.getBoundingClientRect) {
          const bnd = trace2.getBoundingClientRect();
          el.style.left = window.scrollX + bnd.left + "px";
          el.style.top = window.scrollY + bnd.top + "px";
          el.style.width = bnd.width + "px";
          el.style.height = bnd.height + "px";
        }
      }, 50);
      return () => clearInterval(timer);
    }, [props.trace]);
    return createPortal(children, el);
  };

  // ../../mailform/mailer/ui/UploadMedia/component.jsx
  Catpow.UI.UploadMedia = (props) => {
    const { useCallback, useState, useMemo: useMemo2, useRef, useEffect } = React;
    const { createPortal } = ReactDOM;
    const { className = "cmf-ui-uploadmedia", text = "Select File" } = props;
    const { HiddenValues } = Catpow.Components;
    const classes = bem(className);
    const [value, setValue] = useState(props.value || false);
    const [file, setFile] = useState(false);
    const [previewUrl, setPreviewUrl] = useState(false);
    const [message, setMessage] = useState(false);
    const [fileInput, setFileInput] = useState(false);
    const maxFileSizeInt = useMemo2(() => {
      if (!props.filesize) {
        return false;
      }
      return dataSizeStringToInt(props.filesize);
    }, [props.filesize]);
    useEffect(() => {
      if (!fileInput) {
        return;
      }
      const reader = new FileReader();
      reader.addEventListener("load", (e) => {
        setPreviewUrl(reader.result);
      });
      fileInput.addEventListener("change", (e) => {
        const file2 = e.currentTarget.files[0];
        if (file2.size > maxFileSizeInt) {
          setMessage("Too large File");
          setPreviewUrl(false);
          return;
        }
        setMessage(false);
        setFile(file2);
        reader.readAsDataURL(file2);
      });
    }, [fileInput]);
    return /* @__PURE__ */ React.createElement("div", { className: classes() }, /* @__PURE__ */ React.createElement("div", { className: classes.button(), onClick: () => fileInput.click() }, text), message && /* @__PURE__ */ React.createElement("div", { className: classes.message() }, message), previewUrl && /* @__PURE__ */ React.createElement("div", { className: classes.preview() }, /* @__PURE__ */ React.createElement("div", { className: classes.preview.images() }, /* @__PURE__ */ React.createElement("img", { className: classes.preview.images.img(), src: previewUrl })), /* @__PURE__ */ React.createElement("div", { className: classes.preview.spec() }, /* @__PURE__ */ React.createElement("span", { className: classes.preview.spec.name() }, file.name), /* @__PURE__ */ React.createElement("span", { className: classes.preview.spec.size() }, intToDataSizeString(file.size)))), /* @__PURE__ */ React.createElement(Portal, { className: classes.portal() }, /* @__PURE__ */ React.createElement("input", { className: classes.portal.input(), type: "file", accept: props.accept, ref: setFileInput })), value && /* @__PURE__ */ React.createElement(
      HiddenValues,
      {
        name: props.name,
        value
      }
    ));
  };
})();
