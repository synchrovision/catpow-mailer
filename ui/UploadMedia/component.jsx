import { bem, dataSizeStringToInt, intToDataSizeString } from "catpow/util";
import { Portal } from "catpow/component";

const matchesAccept = (file, accept) => {
	const acceptList = accept
		.split(",")
		.map((s) => s.trim())
		.filter(Boolean);
	const mime = file.type;
	const name = file.name;

	return acceptList.some((pattern) => {
		if (pattern.startsWith(".")) {
			return name.toLowerCase().endsWith(pattern.toLowerCase());
		} else if (pattern.endsWith("/*")) {
			const typePrefix = pattern.slice(0, -1);
			return mime.startsWith(typePrefix);
		} else {
			return mime === pattern;
		}
	});
};
Catpow.UI.UploadMedia = (props) => {
	const { useState, useMemo, useCallback, useEffect } = React;
	const { className = "cmf-ui-uploadmedia", text = "Select File", cmf, loaderDots = 5 } = props;
	const { HiddenValues } = Catpow.Components;
	const classes = bem(className);

	const [file, setFile] = useState(false);
	const [isUploading, setIsUploading] = useState(false);
	const [message, setMessage] = useState(false);
	const [fileInput, setFileInput] = useState(false);

	const maxFileSizeInt = useMemo(() => {
		if (!props.filesize) {
			return false;
		}
		return dataSizeStringToInt(props.filesize);
	}, [props.filesize]);

	const updateFile = useCallback(
		(files) => {
			for (let i = 0; i < files.length; i++) {
				if (files[i].size > maxFileSizeInt) {
					setMessage("Too large File");
					return;
				}
				if (!matchesAccept(files[i], props.accept)) {
					setMessage("Invalid File type");
					return;
				}
			}
			setMessage(false);
			setIsUploading(true);
			const data = new FormData();
			data.set(props.name, files[0]);
			cmf.send(data, function (res) {
				setIsUploading(false);
				if (res.error) {
					cmf.showError(res.error);
					cmf.focusAlert();
					return;
				}
				if (res.files && res.files[props.name]) {
					setFile(res.files[props.name]);
				}
			});
		},
		[setMessage, setFile]
	);
	const onDropHandler = useCallback(
		(e) => {
			e.preventDefault();
			if (e.dataTransfer.items) {
				updateFile([...e.dataTransfer.items].filter((item) => item.kind === "file").map((item) => item.getAsFile()));
			} else {
				updateFile(e.dataTransfer.files);
			}
		},
		[updateFile]
	);
	const onFileInputChangeHandler = useCallback(
		(e) => {
			updateFile(e.currentTarget.files);
		},
		[updateFile]
	);

	return (
		<div className={classes({ "is-uploading": isUploading })} onDrop={onDropHandler} onDragOver={(e) => e.preventDefault()}>
			<div className={classes.button()} onClick={() => fileInput.click()}>
				{text}
				{isUploading && (
					<div className={classes.loader()}>
						{[...Array(loaderDots).keys()].map((i) => (
							<div className={classes.loader._dot("is-dot-" + i)} style={{ "--dot-index": i }} key={i}></div>
						))}
					</div>
				)}
			</div>
			{message && <div className={classes.message()}>{message}</div>}
			{file && (
				<div className={classes.preview()} key={file.name}>
					<div className={classes.preview.images()}>
						<img className={classes.preview.images.img()} src={cmf.getFileUrl(props.name) + "&fname=" + file.file_name} />
					</div>
					<div className={classes.preview.spec()}>
						<span className={classes.preview.spec.name()}>{file.name}</span>
						<span className={classes.preview.spec.size()}>{intToDataSizeString(file.size)}</span>
					</div>
				</div>
			)}
			<Portal className={classes.portal()}>
				<form className={classes.portal.form()}>
					<input className={classes.portal.input()} type="file" name={props.name} accept={props.accept} onChange={onFileInputChangeHandler} ref={setFileInput} />
				</form>
			</Portal>
			{file && <HiddenValues name={props.name} value={file} />}
		</div>
	);
};
