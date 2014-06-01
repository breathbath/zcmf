ob = {

    initialize: function () {

        this.errors = 0;
        this.enabled = true;
        setTimeout(this.init.bind(this), 500);
    },

     init: function () {
     	this.percent(0);
     	this.status('begin','','');
     	$('#progress').show();
        $.getJSON(
            "/admin/z_backup/init",
            function (r) {
                try {
                    if (r.steps) {
                        this.steps = r.steps;
                        this.stepAmount = this.steps.length;
                        window.setTimeout(this.process.bind(this), 500);
                    }
                    else {
                        this.message('Не получен сценарий сохранения');
                        this.error();
                    }
                }
                catch (e) {
                    this.message('Ошибка инициализации сценария');
                    this.error();                  
                }
            }.bind(this)
       );
    },
    
    
    
    
    process: function (definedJob) {

        if (!this.enabled) {
            return;
        }
        //alert (this.stepAmount);
        //return false;

        var status = (1 - (this.steps.length / this.stepAmount));
        var percent = Math.ceil(status * 100);
        var filesize = "";
        var fileAmount = "";

        if (this.lastResponse) {
        	var fp='';
            if (this.lastResponse.filesize) {
                filesize = this.lastResponse.filesize;
            }
            if (this.lastResponse.fileAmount) {
                fileAmount = this.lastResponse.fileAmount;
            }
        }

        //this.progressBar.updateProgress(status, percent + "%" + filesize + fileAmount);
        
        //var box=$('#log');
        //box.val(box.val() + "\n"+"("+status+")"+percent + "%" + filesize + fileAmount);
        this.percent(percent);

        if (this.steps.length > 0) {

            var nextJob;
            if (typeof definedJob == "object") {
                nextJob = definedJob;
            }
            else {
                nextJob = this.steps.shift();
            }
            this.status(nextJob[0],filesize,fileAmount)
            $.getJSON(
                "/admin/z_backup/" + nextJob[0],
               nextJob[1],
                function (job,json) {
                      try {
                        if (json.success) {
                            this.lastResponse = json;
                            window.setTimeout(this.process.bind(this), 500);
                        }
                        else {
                        	this.message('Ошибка операции '+nextJob[0]);
                            this.error(job);
                        }
                    }
                    catch (e) {
                    	this.message('Неизвестная ошибка сценария');
                        this.error(job);
                    }
                }.bind(this, nextJob)
            );
        }
        else {
          	 window.setTimeout(this.success.bind(this), 500);     
        }
    },
    success: function () {
    	this.status('theend','','');
    	$('#nobackups').hide();
    	var date='';
    	var size='';
    	var link='#';
    	if (this.lastResponse.filedate){
    		date =this.lastResponse.filedate;
    	}	
    	if (this.lastResponse.filesize){
    		size= this.lastResponse.filesize;
    	}	
    	if (this.lastResponse.fileid){
    		link='/admin/z_backup/download/id/'+this.lastResponse.fileid;		 
    	}
    	$("#z-grid").append('<tr class="ui-widget-content"><td>'+date+'<br></td><td>'+size+'<br></td><td><a href="'+link+'">Скачать</a><br></td><td></td></tr>');
    	window.setTimeout(this.hideProgress.bind(this), 500);

    },
    hideProgress: function(){
    	$('#progress').fadeOut(3000);
    },
    message: function(text){
    	$("ul#upload_error").append (
    		$('<li>'+text+'</li>')
    	);
    },
    
    status: function(job,filesize,filecount){
    	var status='';
    	switch (job) {
    		case 'begin':
    			status='Формирую сценарий';
    			break;
   			case 'mysql-tables':
      			status='Копирую структуру базы данных';
      			break;
		   case 'mysql':
		   		status='Сохраняю информацию из таблиц базы данных';
		   		break;
		   case 'files':
		   		status='Архивирую файлы: Добавлено файлов: '+filecount+' / Размер архива: '+filesize;
		   		break;
		   case 'complete':
		   		status='Завершаю процесс';
		   		break;
		   case 'theend':
		   		status='Резервная копия успешно создана!';
		   		break;		
		   default:
		   		return false;						
     	}
    	$(".progressBarStatus #text").text(status);
    },
    
    percent: function(i){
    	$(".progressBarStatus #percent").text(i+"%");
    	$(".progressBarComplete").width(i+"%");	
    },

    error: function (job) {

        var hasNoJob;
        if (typeof job == "object") {
            hasNoJob = true;
        }

        if (this.errors > 30 || hasNoJob) {
            this.enabled = false;
            var box=$('#log');
        	box.val(box.val() + "\nError");
            return;
        }
        else {
            window.setTimeout(this.process.bind(this, job), 500);
        }

        this.errors++;
    },
    stop: function (){
    	this.enabled=false;
    }
}

function backuping()
{
	ob.initialize();	
}
$(document).ready(function(){
	$("#stop").click (function(){
		ob.stop();
		//alert(ob.enabled);
		return false;
		
	}.bind(ob));
});
