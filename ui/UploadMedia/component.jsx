import {bem,dataSizeStringToInt,intToDataSizeString} from 'util';
import {Portal} from 'component';

Catpow.UI.UploadMedia=(props)=>{
	const {useCallback,useState,useMemo,useRef,useEffect}=React;
	const {createPortal}=ReactDOM;
	const {className="cmf-ui-uploadmedia",text='Select File'}=props;
	const {HiddenValues}=Catpow.Components;
	const classes=bem(className);
	
	const [value,setValue]=useState(props.value || false);
	const [file,setFile]=useState(false)
	const [previewUrl,setPreviewUrl]=useState(false);
	const [message,setMessage]=useState(false);
	const [fileInput,setFileInput]=useState(false);
	
	const maxFileSizeInt=useMemo(()=>{
		if(!props.filesize){return false;}
		return dataSizeStringToInt(props.filesize);
	},[props.filesize]);
	
	useEffect(()=>{
		if(!fileInput){return;}
		const reader=new FileReader();
		reader.addEventListener('load',(e)=>{
			setPreviewUrl(reader.result);
		});
		fileInput.addEventListener('change',(e)=>{
			const file=e.currentTarget.files[0];
			if(file.size>maxFileSizeInt){
				setMessage('Too large File');
				setPreviewUrl(false);
				return;
			}
			setMessage(false);
			setFile(file);
			reader.readAsDataURL(file);
		});
	},[fileInput]);

	return (
		<div className={classes()}>
			<div className={classes.button()} onClick={()=>fileInput.click()}>{text}</div>
			{message && <div className={classes.message()}>{message}</div>}
			{previewUrl && (
				<div className={classes.preview()}>
					<div className={classes.preview.images()}>
						<img className={classes.preview.images.img()} src={previewUrl}/>
					</div>
					<div className={classes.preview.spec()}>
						<span className={classes.preview.spec.name()}>{file.name}</span>
						<span className={classes.preview.spec.size()}>{intToDataSizeString(file.size)}</span>
					</div>
				</div>
			)}
			<Portal className={classes.portal()}>
				<input className={classes.portal.input()} type="file" accept={props.accept} ref={setFileInput}/>
			</Portal>
			{value && (
				<HiddenValues
					name={props.name}
					value={value}
				/>
			)}
		</div>
	);
}