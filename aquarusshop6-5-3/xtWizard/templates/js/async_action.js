/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */
 
(function(window, $){
	
	var AsyncLoader = function(url, textHideLog, textShowLog) {
		// Base propertios
		this.AsyncUrl = url;
		this.AsyncInterval = 500;
		this.Progress = 0;
		this.HasNext = true;
		this.Offset = 0;
		this.Interval = null;
		this.Loading = false;
		this.ExtraParams = {};
		this.NextUrl = "";
		
		// Dom elements
		this.ProgressBarId = "#ProgressBar";
		this.PanelLogClass = ".panel-log";
		this.CurrentOperationId = "#CurrentOperation";
		this.ButtonNextPage = ".btn-next-page";
		this.LoadingIcon = ".loading-icon";

        this.TextShowLog = textShowLog;
        this.TextHideLog = textHideLog;

		var self = this;
		
		this.setProgress = function(value) {
			$(self.ProgressBarId).css('width', value + "%");
			self.Progress = value;
		}
		
		this.setCurrentMessage = function(text) {
			$(self.CurrentOperationId).html(text);
		}
		
		this.addLog = function(html) {
			$(self.PanelLogClass).append(html).animate({ scrollTop: $('.panel-log')[0].scrollHeight}, 1000);
		}
		
		this.load = function() {
			self.Loading = true;
			params = {ajaxLoad : 1, offset : self.Offset, Progress : self.Progress};
			
			for (var i in self.ExtraParams) {
				if (self.ExtraParams.hasOwnProperty(i)) {
					params[i] = self.ExtraParams[i]; 
				}
			}
			$.ajax({
				type: "GET",
				url: self.AsyncUrl,
				data: params,
				dataType:'json',
				success: function(data) {

					self.Loading = false;
                    self.HasNext = data.HasNext;
                    self.addLog(data.LogMessages);
                    self.setProgress(data.Progress);
                    self.setCurrentMessage(data.CurrentMessage);
                    self.ExtraParams = data.ExtraParams;
					$(self.ButtonNextPage).attr('href', data.NextUrl);

					if(data.hasError == true)
					{
						if (data.Offset == -1)
						{
                            self.Offset = data.Offset = self.Offset + 1;
						}
                        else {
                            self.Offset = data.Offset;
                            self.NextUrl = data.NextUrl;
                        }

                        if ($(self.PanelLogClass).is(':visible'))
						{
							// nix
						}
						else {
                            $(self.PanelLogClass).parent().slideToggle('slow', function() {
                                if ($(this).is(':visible'))
                                    $('.log-status').html(self.TextHideLog);
                                else
                                    $('.log-status').html(self.TextShowLog);
                            });
						}
					}
					else {
                        self.Offset = data.Offset;
                        self.NextUrl = data.NextUrl;
					}
					
					if (!self.HasNext) {
						window.clearInterval(self.Interval);
						$(self.LoadingIcon).hide();
                        $(self.ProgressBarId).removeClass('progress-bar-striped active');
						$(self.ButtonNextPage).show();
					}
				},
				error: function(response)
				{
					console.log(response);

					var data = response.responseJSON;

                    self.Loading = false;
                    self.Offset = self.Offset + 1;
                    self.HasNext = data.HasNext;
                    self.addLog(data.LogMessages);
                    self.setProgress(data.Progress);
                    self.setCurrentMessage(data.CurrentMessage);
                    self.ExtraParams = data.ExtraParams;
                    $(self.ButtonNextPage).attr('href', self.NextUrl);

                    if(data.hasError == true)
                    {
                        if ($(self.PanelLogClass).is(':visible'))
                        {
                            // nix
                        }
                        else {
                            $(self.PanelLogClass).parent().slideToggle('slow', function() {
                                if ($(this).is(':visible'))
                                    $('.log-status').html(self.TextHideLog);
                                else
                                    $('.log-status').html(self.TextShowLog);
                            });
                        }
                    }
				}
			});
		}
		
		this.setProgress(this.Progress);
		this.Interval = window.setInterval(function(){
			if (self.Loading) {
				return;
			}
			self.load();
		}, self.AsyncInterval);
		self.load();
	}
	
	window.createAsyncLoader = function(url, textHideLog, textShowLog) {
		return new AsyncLoader(url, textHideLog, textShowLog);
	}
})(window, jQuery);