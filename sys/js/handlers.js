
function fileQueueError(file, errorCode, message) {
    try {
        //alert(file.name);
        if(file!=null)
        {    
           var progress = new FileProgress(file,  this.customSettings.upload_target);
            progress.setStatus("Ошибка файла.");
            progress.toggleCancel(false);
            progress.setError();    
        }
        errorName="Ошибка";

        switch (errorCode) {
        case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
            errorName = "Разрешена одновременная загрузка не более "+this.settings['file_queue_limit']+" файлов!";
            break;    
        case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
            errorName = "Размер файла '"+file.name+"' больше установленного лимита в "+this.settings['file_size_limit']+" МБ!";
            break;
        case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            errorName = "Загружаемый файл '"+file.name+"' пустой!";
            break;    
        case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
            errorName = "Неправильный тип файла '"+file.name+"'!";
            break; 
        }
        //Андрей Юрьевич 0895512453 360
       
        errMess(errorName,this.customSettings.error_div);

    } catch (ex) {
        this.debug(ex);
    }
}
/**
 * Comment
 */
function errMess(mess,target) {
    //alert(target);
    // clearErrMess(target);
   var li = document.createElement('li'); 
   li.innerHTML = mess;
   var doc= document.getElementById(target);
//   //var err = document.createElement("div");
   doc.className = "ers";
   doc.appendChild(li);
//   ///doc.appendChild(err);
}
/**
 * 
 */
function clearErrMess(target) {
  var doc= document.getElementById(target);
  doc.innerHTML="";
//  for( var i = 0; i < doc.childNodes.length; i++ ) {
//  if (doc.childNodes[i].className=="ers") {
//     doc.removeChild(doc.childNodes[i]);
//   }
//  }
}
function fileDialogStart (){
 this.qsize=0;
 this.uploaded =0;
 clearErrMess(this.customSettings.error_div);
 
}
/**
 * Comment
 */
function clearList() {
        doc = document.getElementById("image_list");
        var len = doc.childNodes.length;
                    for( var i = 0; i < len; i++ ) {
                         doc.removeChild(doc.childNodes[0]);
                    }
}
function fileDialogComplete(numFilesSelected, numFilesQueued) {
	//alert (numFilesSelected+"---"+numFilesQueued);
        try {
                if (numFilesQueued > 0) {
                    //alert(numFilesQueued);
//                    doc = document.getElementById("image_list");
//                    var len = doc.childNodes.length;
//                    for( var i = 0; i < len; i++ ) {
//                         doc.removeChild(doc.childNodes[0]);
//                    }
                    this.fq = numFilesQueued;
                        this.curr++;
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadStart(file){
    
//   var progress = new FileProgress(file,  this.customSettings.upload_target);
//   progress.setStatus("Грузим файлы 0 из "+this.fq); 
}
function fileQueue (file){
  if(this.qsize>0)
      this.qsize=this.qsize+file.size;
  else
      this.qsize=file.size;
   //this.curr=file.size;
    
}
function swfUploadLoaded()
{
    
    var element;
    element = document.getElementById("pro_image");
    if(element)
    {    
    element.style.display = "none";
    }
    element = document.getElementById("pro_video");
    if(element)
    {element.style.display = "none";}    
    element = document.getElementById("pro_autor_image");
    if(element)
    {    
    element.style.display = "none";
    }
    
}

function uploadProgress(file, bytesLoaded) {

	try {
		//alert(this.customSettings.upload_target);
                //clearErrMess(this.customSettings.error_div);
                var percent = Math.ceil(((bytesLoaded+this.uploaded) / this.qsize) * 100);
                
//alert(this.customSettings.upload_target);
		var progress = new FileProgress(file,  this.customSettings.upload_target);
                this.debug('percent: '+percent);
		progress.setProgress(percent);
                progress.setStatus("Загружено "+percent+"%");
//		if (percent === 100) {
//			progress.setStatus("Загружаем... "+this.curr+" из "+this.fq+" Всего "+this.me);
//			progress.toggleCancel(false, this);
//		} else {
//			progress.setStatus("Загружаем... "+this.curr+" из "+this.fq+" Всего "+this.me);
//			progress.toggleCancel(true, this);
//		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
    try {
        var progress = new FileProgress(file,  this.customSettings.upload_target);
        var x = eval("(" + serverData + ")");
        if (x.error==0) {
            var elid="";
            if(x.type=="pic")
            {
                elid="url";
            }    
            else if (x.type=="vid") {
                elid="videourl";    
            }
            else if (x.type=="apic") {
                elid="profileurl";    
            }
            if(elid)
            {    
                addImage(x.preview,file.name,x.path);
                var element = document.getElementById(elid);
                element.setAttribute("param", "/pf/"+x.fileid);
            }
            //addImage(serverData.substring(7),serverData);
            //sys/generatepreview.php?w=20&amp;h=20&amp;file=%2Fstorage%2FLScBna3x%2Fkukl.jpg
            //alert (serverData.substring(7));
            //addImage(serverData.substring(7));
            progress.setComplete();
        
            progress.setStatus("Загрузка завершена!");
            progress.toggleCancel(false); 
        } else {
			
            progress.setStatus("Ошибка файла.");
            progress.toggleCancel(false);
            progress.setError();
                
        }


    } catch (ex) {
        this.debug(ex);
    }
}
function uploadComplete(fileObj) {
	try {
		if(this.uploaded>0)
                    this.uploaded=this.uploaded+fileObj.size;
                else
                    this.uploaded = fileObj.size;
                if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(fileObj,  this.customSettings.upload_target);
                       // progress.setProgress(100);
			progress.setStatus("Загружено 100%");
                        progress.setComplete();
			progress.ToggleCancel(false);
		}
	} catch (ex) {this.debug(ex);}
}

function uploadError(file, errorCode, message) {
	 var progress = new FileProgress(file,  this.customSettings.upload_target);
            progress.setStatus("Ошибка файла.");
            progress.toggleCancel(false);
            progress.setError();
	//var progress;
        try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				//progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Отмена");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				//progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Остановлено");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			errMess("Ошибка загрузки. Исчерпан загрузочный лимит!",this.customSettings.error_div);
			break;
		default:
			errMess(message,this.customSettings.error_div);
			break;
		}

	} catch (ex3) {
		this.debug(ex3);
	}

}


function addImage(preview,filename,src) {
        block = document.getElementById('im_block');
        block.style.display='block';
        element = document.getElementById("image_list");
        var li = document.createElement('li');
        var checkbox = document.createElement("input");
        checkbox.type="checkbox";
        checkbox.name="flag []";
        checkbox.id="flag"+src;
        checkbox.checked="checked";
        checkbox.value=src+"@"+filename;
        checkbox.className="z-form-checkbox";
        li.appendChild(checkbox);
        li.appendChild(document.createElement("br"));
        var newImg = document.createElement("img");
        li.appendChild(newImg);
        element.appendChild(li);
	if (newImg.filters) {
		try {
			newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			newImg.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=" + 0 + ")";
		}
	} else {
		newImg.style.opacity = 0;
	}

	newImg.onload = function () {
		fadeIn(newImg, 0);
	};
	newImg.src = preview;
}

function fadeIn(element, opacity) {
	var reduceOpacityBy = 5;
	var rate = 30;	// 15 fps


	if (opacity < 100) {
		opacity += reduceOpacityBy;
		if (opacity > 100) {
			opacity = 100;
		}

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=" + opacity + ")";
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function () {
			fadeIn(element, opacity);
		}, rate);
	}
}



/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = file.id;

	this.fileProgressWrapper = document.getElementById("progressWrapper");
       // this.fileProgressWrapper.style.display="auto";
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = "progressWrapper";

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

                
                var progressBarContainer = document.createElement("div");
		progressBarContainer.className = "progressBar";

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";
               
		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";
                var progressHide = document.createElement("a");
		progressHide.className = "closer";
		progressHide.innerHTML = "(Скрыть)";
                progressHide.onclick=function(){document.getElementById(file.id).style.display = "none";return false;};
		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressBarContainer).appendChild(progressBar);
                this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		fadeIn(this.fileProgressWrapper, 0);

	} else {
            this.fileProgressWrapper.style.display="inline-block";
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[1].childNodes[0].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[1].childNodes[0].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[1].childNodes[0].className = "progressBarComplete";
	this.fileProgressElement.childNodes[1].childNodes[0].style.width = "100%";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[1].childNodes[0].className = "progressBarError";
	this.fileProgressElement.childNodes[1].childNodes[0].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[1].childNodes[0].className = "progressBarError";
	this.fileProgressElement.childNodes[1].childNodes[0].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};      
