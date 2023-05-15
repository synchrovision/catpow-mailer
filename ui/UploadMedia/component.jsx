import {bem,dataSizeStringToInt,intToDataSizeString} from 'util';
import {Portal} from 'component';

Catpow.UI.UploadMedia=(props)=>{
	const {useCallback,useState,useMemo,useRef,useEffect}=React;
	const {createPortal}=ReactDOM;
	const {className="cmf-ui-uploadmedia",text='Select File',cmf}=props;
	const {HiddenValues}=Catpow.Components;
	const classes=bem(className);
	
	const [file,setFile]=useState(false);
	const [previewUrl,setPreviewUrl]=useState(false);
	const [message,setMessage]=useState(false);
	const [portalForm,setPortalForm]=useState(false);
	const [fileInput,setFileInput]=useState(false);
	
	const maxFileSizeInt=useMemo(()=>{
		if(!props.filesize){return false;}
		return dataSizeStringToInt(props.filesize);
	},[props.filesize]);
	
	useEffect(()=>{
		if(!fileInput){return;}
		fileInput.addEventListener('change',(e)=>{
			const files=e.currentTarget.files;
			for(let i=0;i<files.length;i++){
				if(files[i].size>maxFileSizeInt){
					setMessage('Too large File');
					setPreviewUrl(false);
					return;
				}
			}
			setMessage(false);
			const data=new FormData(portalForm);
			cmf.send(data,function(res){
				if(res.error){
					cmf.showError(res.error);
					cmf.focusAlert();
					return;
				}
				if(res.files && res.files[props.name]){
					setFile(res.files[props.name]);
				}
			});
		});
	},[portalForm,fileInput]);

	return (
		<div className={classes()}>
			<div className={classes.button()} onClick={()=>fileInput.click()}>{text}</div>
			{message && <div className={classes.message()}>{message}</div>}
			{file && (
				<div className={classes.preview()} key={file.name}>
					<div className={classes.preview.images()}>
						<img className={classes.preview.images.img()} src={cmf.getFileUrl(props.name)}/>
					</div>
					<div className={classes.preview.spec()}>
						<span className={classes.preview.spec.name()}>{file.name}</span>
						<span className={classes.preview.spec.size()}>{intToDataSizeString(file.size)}</span>
					</div>
				</div>
			)}
			<Portal className={classes.portal()}>
				<form className={classes.portal.form()} ref={setPortalForm}>
					<input className={classes.portal.input()} type="file" name={props.name} accept={props.accept} ref={setFileInput}/>
				</form>
			</Portal>
			{file && (
				<HiddenValues
					name={props.name}
					value={file}
				/>
			)}
		</div>
	);
}