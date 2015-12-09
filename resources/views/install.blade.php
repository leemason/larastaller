<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>{{ $larastaller['application_name'] }} {{ $version->version }} Installer</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style type="text/css">
        .alert{
            padding: 10px;
            margin-bottom: 10px;
        }
        #installer-logo{
            margin-top: 60px;
            margin-bottom: 60px;
        }
        #install-information{
            max-height: 200px;
            overflow: scroll;
            margin-bottom: 20px;
            font-family: 'Monaco', courier, monospace;
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <p class="text-center" id="installer-logo">@if($larastaller['installer_logo'] != '')<img src="{{ $larastaller['installer_logo'] }}"/>@endif</p>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <!-- Welcome Panel -->
            <div class="panel panel-default" id="welcome">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $larastaller['application_name'] }} {{ $version->version }} <a href="#" class="pull-right btn-xs btn-link" data-toggle="modal" data-target="#changes">View Changes</a></h3>
                </div>
                <div class="panel-body">
                    <p>Welcome to the installer!</p>
                    <p>You will now be guided through the steps needed to install {{ $larastaller['application_name'] }}.</p>
                    <a href="#" class="btn btn-default btn-block">Continue</a>
                </div>
            </div>

            <!-- Changes Modal -->
            <div class="modal fade" id="changes" tabindex="-1" role="dialog" aria-labelledby="changesLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">{{ $larastaller['application_name'] }} {{ $version->version }} Changes</h4>
                        </div>
                        <div class="modal-body">
                            <ul>
                                @foreach($version->getChanges() as $change)
                                    <li>{{ $change }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements Panel -->
            <div class="panel panel-default" id="requirements" style="display: none;">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $larastaller['application_name'] }} {{ $version->version }} Requirements</h3>
                </div>
                <div class="panel-body">
                    @foreach($requirements_messages->get('success') as $message)
                        <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> {{ $message }}</div>
                    @endforeach
                    @foreach($requirements_messages->get('error') as $message)
                        <div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> {{ $message }}</div>
                    @endforeach
                    @if($requirements_messages->has('error'))
                        <p>Whoops, it looks like your system couldn't pass all of the requirements! Please address the error(s) and re-run the installer.</p>
                        <a href="{{ url('/install') }}" class="btn btn-default btn-block">Try Again</a>
                    @else
                        <p>Congratulations, your server environment is suitable for {{ $larastaller['application_name'] }}.<br/>Click continue to proceed.</p>
                        <a href="#" class="btn btn-default btn-block" id="passed">Continue</a>
                    @endif
                </div>
            </div>

            <!-- Task Fields Panel -->
            <div class="panel panel-default" id="task-fields" style="display: none;">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $larastaller['application_name'] }} {{ $version->version }} Data</h3>
                </div>
                <div class="panel-body">
                    <form method="post" id="task-fields-form" action="{{ route('installer.validate') }}">
                        {{ csrf_field() }}
                        @foreach($tasks as $task)
                            @if(count($task->getFields()) > 0)
                                <h4>{{ $task->getTitle() }}</h4>
                                <hr/>
                                <p>{{ $task->getDescription() }}</p>
                                @foreach($task->getFields() as $field)
                                    <div class="form-group {{ $field->getID() }}-group">
                                        <label class="control-label" for="{{ $field->getID() }}">{{ $field->getLabel() }}</label>
                                        <input type="text" class="form-control" id="{{ $field->getID() }}" name="{{ $field->getID() }}" placeholder="{{ $field->get('placeholder', '') }}" value="{{ old($field->getID()) }}">
                                        <p class="help-block field-desc">{{ $field->getDescription() }}</p>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                        <p><a href="#" class="btn btn-default btn-block" id="submit-fields">Continue</a></p>
                    </form>
                </div>
            </div>

            <!-- Progress Panel -->
            <div class="panel panel-default" id="progress" style="display: none;">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $larastaller['application_name'] }} {{ $version->version }} Installing</h3>
                </div>
                <div class="panel-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                    <div id="install-information"></div>
                    <a href="#" class="btn btn-default btn-block hidden" id="completed">Installation Successful</a>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script>
    (function($){

        var installerValues = {};

        // move from welcome to requirements
        $('#welcome').on('click', '.panel-body a', function(e){
            e.preventDefault();
            $('#welcome').fadeOut('fast', function(){
                $('#requirements').fadeIn('fast');
            });
            return false;
        });

        // move from requirements to data submittion
        $('#requirements').on('click', '#passed', function(e){
            e.preventDefault();
            $('#requirements').fadeOut('fast', function(){
                $('#task-fields').fadeIn('fast');
            });
            return false;
        });

        // move from submission to task run
        $('#task-fields').on('click', '#submit-fields', function(e){
            e.preventDefault();
            var form = $(this).closest('form');

            //reset form validation
            $('.help-block:not(.field-desc)', form).remove();
            $('.help-block.hidden', form).removeClass('hidden');
            $('.has-error', form).removeClass('has-error');

            //validate
            $.post(form.attr('action'), form.serialize(), function(response){
                if(response.status == 'success'){
                    //move on
                    installerValues = response.values;
                    $('#task-fields').fadeOut('fast', function(){
                        $('#progress').fadeIn('fast');
                        $('#progress').trigger('click');
                    });
                }else if(response.status == 'error'){
                    $.each(response.errors, function(field, errors){
                        $('.' + field + '-group .field-desc').addClass('hidden');
                        $('.' + field + '-group').addClass('has-error');
                        var currentField = field;
                        $.each(errors, function(index, error){
                            $('.' + currentField + '-group').append('<p class="help-block">' + error + '</p>');
                        });
                    });
                }
            });
            return false;
        });

        //display install info
        $('#progress').on('click', function(){
            $.post('{{ route('installer.post') }}', installerValues, function(response){
                if(response.status == 'success'){
                    $('#install-information').html(response.output.join('<br/>'));
                    $('#completed').removeClass('hidden');
                }else if(response.status == 'error'){
                    $('#install-information').html(response.output.join('<br/>'));
                }
            });
        });

        // display complete panel

    })(jQuery);
</script>
</body>
</html>