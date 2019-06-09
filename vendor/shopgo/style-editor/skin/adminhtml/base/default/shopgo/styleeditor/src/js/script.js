var logger = function () {
    var oldConsoleLog = null;
    var pub = {};

    pub.enableLogger = function enableLogger() {
        if (oldConsoleLog == null)
            return;

        window['console']['log'] = oldConsoleLog;
    };

    pub.disableLogger = function disableLogger() {
        oldConsoleLog = console.log;
        window['console']['log'] = function () {
        };
    };

    return pub;
}();

//
// ShopGo StyleEditor Module
var cw,
    styleEditorApp = angular.module('styleEditorApp', ['angularFileUpload']);

styleEditorApp.factory('styleEditorService', function () {
    return {
        showLoader: function () {
            $("#loader").fadeIn("slow");
        },
        hideLoader: function () {
            $("#loader").fadeOut("slow");
        }
    }
});


styleEditorApp.directive('iframeOnload', ['styleEditorService', function (styleEditorService) {
    return {
        scope: {
            callBack: '&iframeOnload'
        },
        link: function (scope, element, attrs) {
            element.on('load', function () {
                styleEditorService.hideLoader();
                return scope.callBack();
            })
        }
    }
}]);

styleEditorApp.controller('iframeController', ['$scope', function ($scope) {

    angular.element('#preview-iframe').attr('src', baseUrl);

    $scope.iframeLoadedCallBack = function () {

        var agent = [];
        var previewIframe = document.getElementById("preview-iframe");

        agent[0] = document.createElement('link');
        agent[0].setAttribute('rel', 'stylesheet/less');
        agent[0].setAttribute('data-global-vars', '{"skin-path": "\'' + theme_custom + '\'" }');
        agent[0].setAttribute('href', theme_dir + 'src/less/theme.less');

        agent[1] = document.createElement("script");
        agent[1].type = "text/javascript";
        agent[1].text = "less = {env: 'development',logLevel: 0};";

        agent[2] = document.createElement("script");
        agent[2].type = "text/javascript";
        agent[2].src = "https://cdnjs.cloudflare.com/ajax/libs/less.js/2.4.0/less.min.js";
        agent[2].onload = function(e){cw.seagent.modifyVars(window.lessVars);};

        var agentsrc = "\
            var StyleEditorAgent = function (themeName) {\
                this.themeName = themeName;\
            };\
            StyleEditorAgent.prototype.modifyVars = function (vars) {\
                less.modifyVars(vars);\
                less.refreshStyles();\
            };\
            var seagent = new StyleEditorAgent('');\
         ";
        agent[3] = document.createElement("script");
        agent[3].type = "text/javascript";
        agent[3].text = agentsrc;

        for (var i = 0; i < agent.length; i++) {
            previewIframe.contentWindow.document.head.appendChild(agent[i]);
        }

        cw = previewIframe.contentWindow;
        setTimeout(function(){
            jQuery(".sui-forms .gui-input").trigger("change");
        }, 500);
    }
}]);

styleEditorApp.controller('saveController', ['$scope', '$http', function ($scope, $http) {
    //
    $scope.formData = {};
    // save
    $scope.saveForm = function () {

        var fields = $('#sui-form').serializeArray();
        var variables = new Object;
        jQuery.each(fields, function (i, field) {
            variables[field.name] = field.value;
        });

        var images = $('.gui-file');
        jQuery.each(images, function (i, image) {
            if (image.className === 'gui-file valid') {
                variables[image.name] = '"' + theme_custom_images +  $(image).val().split('\\').pop() + '"';
            } else {
                variables[image.name] = '"' + theme_custom_images + image.defaultValue + '"';
            }
        });

        $scope.formData = variables;

        NProgress.start();
        $('#sui-form button[type="submit"]').prop('disabled', true);
        $http({
            method: 'POST',
            url: save_index,
            data: $.param($scope.formData),  // pass in data as strings
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}  // set the headers so angular passing info as form data (not request payload)
        })
            .success(function (data) {
                NProgress.done();
                $('#sui-form button[type="submit"]').prop('disabled', false);
                if (data.status !== 'success') {
                    //console.log(data.message);
                    return swal({
                        title: 'Error',
                        text: "Oops! something went wrong.",
                        type: "error",
                        confirmButtonText: "Ok"
                    });
                } else {
                    return swal({title: "Saved!", text: data.message, type: "success", confirmButtonText: "Ok"});
                }
            });
    };
}]);


styleEditorApp.controller('fileuploader', ['$scope', 'FileUploader', function ($scope, FileUploader) {
    var uploader = $scope.uploader = new FileUploader({
        url: upload_index,
        autoUpload: true,
    });

    // FILTERS

    uploader.filters.push({
        name: 'sizeFilter',
        fn: function (item) {
            if (item.size <= 1048576) {
                return true;
            } else {
                swal({
                    title: "Large image size!",
                    text: "Your image size should be smaller than 1MB",
                    type: "warning",
                    html: true
                });
                invalid(item.name);
                return false;
            }
        }
    });

    uploader.filters.push({
        name: 'typeFilter',
        fn: function (item) {
            if (/\/(png|jpeg|jpg|gif)$/.test(item.type) === true) {
                return true;
            } else {
                swal({
                    title: "Unsupported file type!",
                    text: "Your uploaded file type is not allowed. <br /> you can only upload (.PNG | .JPEG | .JPG | .GIF) images.",
                    type: "warning",
                    html: true
                });
                invalid(item.name);
                return false;
            }
        }
    });


    // CALLBACKS

    uploader.onBeforeUploadItem = function (item) {
        item.formData.push({
            form_key: window.FORM_KEY
        });
    };

    uploader.onAfterAddingFile = function (fileItem) {
        NProgress.start();
    };

    uploader.onCompleteAll = function () {
        liveless();
        uploader.clearQueue();
        NProgress.done();
    };

    uploader.onSuccessItem = function (fileItem, response, status, headers) {
        valid(fileItem.file.name);
    };

}]);

var injector,
    styleEditorService;

angular.element(document).ready(function () {
    //
    // logger.disableLogger();
    injector = angular.element(document.body).injector();
    styleEditorService = injector.get('styleEditorService');
});


function liveless() {
    NProgress.start();
    var fields = $('#sui-form').serializeArray();
    var variables = new Object;
    jQuery.each(fields, function (i, field) {
        variables[field.name] = field.value;
    });

    var images = $('.gui-file');
    jQuery.each(images, function (i, image) {
        if (image.className === 'gui-file valid') {
            variables[image.name] = '"' + theme_custom_images + $(image).val().split('\\').pop() + '"';
        } else {
            variables[image.name] = '"' + theme_custom_images + image.defaultValue + '"';
        }
    });


    delete variables.form_key;
    cw.seagent.modifyVars(variables);
    if (!cw.seagent) {
        return swal({
            title: "Session expired!",
            text: "Style editor session is expired, please reload the page.",
            type: "warning",
            confirmButtonText: "Ok"
        });
    }
    NProgress.done();
    window.lessVars = variables;
};

function valid(itemName) {
    $(".gui-file").filter(function () {
        return $(this).val().split('\\').pop() === itemName;
    }).addClass('valid');
}

function invalid(itemName) {
    $(".gui-file").filter(function () {
        return $(this).val().split('\\').pop() === itemName;
    }).removeClass('valid');
}

function imagefield(name) {
    $('.' + name + '-file').val($('#' + name + '-file').val().split('\\').pop());
}

$(document).ready(function () {

    $(".sui-forms .gui-input").change(liveless);

    //Reset button
    $('#sui-reset').on('click', function (e) {
        e.preventDefault();
        NProgress.start();

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the changes!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, reset it!",
            closeOnConfirm: false
        }, function () {
            $(".gui-file").removeClass('valid');
            $("#sui-form").trigger("reset");
            $(".sui-forms .gui-input").trigger("change");
            var $spfields = $('.sfcolor .gui-input');
            $spfields.each(function () {
                $(this).spectrum("set", $(this).val());
            });
            swal("Success", "Your changes have been reset.", "success");
        });

        NProgress.done();
    });

    materialDesignHamburger();
    $("#sidebar-toggle").click(function () {
        if ($(this).data('name') == 'show') {
            $("#sidebar").css({
                width: '200px'
            });
            $("#iframeContainer").css({
                left: '200px'
            });
            $(this).data('name', 'hide')
        } else {
            $("#sidebar").css({
                width: '0'
            });
            $("#iframeContainer").css({
                left: '0'
            });
            $(this).data('name', 'show')
        }
    });

});