<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 04/12/15
 * Time: 16:59
 */

namespace LeeMason\Larastaller\Http\Controllers;


use Illuminate\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LeeMason\Larastaller\Definition;
use LeeMason\Larastaller\Events\AfterInstallEvent;
use LeeMason\Larastaller\Events\BeforeInstallEvent;
use LeeMason\Larastaller\Installer;
use LeeMason\Larastaller\TaskRunException;
use Symfony\Component\Console\Output\BufferedOutput;

class InstallController extends Controller
{

    public function getInstall(Dispatcher $dispatcher, Installer $installer, Definition $definition){

        $dispatcher->fire(new BeforeInstallEvent($installer, new BufferedOutput()));

        $messages = $installer->testRequirements();

        $tasks = [];
        $versions = $installer->getVersions();
        foreach($versions as $version){
            foreach($installer->getTasksForVersion($version) as $task){
                $tasks[] = $task;
            }
        }

        return view('larastaller::install')->with([
            'larastaller' => config('larastaller'),
            'version' => $definition->getLatestVersion(),
            'requirements_messages' => $messages,
            'tasks' => $tasks
        ]);
    }

    public function postInstallValidate(Request $request, Installer $installer){

        $responseData = [
            'values' => [],
            'errors' => []
        ];

        $versions = $installer->getVersions();
        foreach($versions as $version){
            foreach($installer->getTasksForVersion($version) as $task){
                if(count($task->getFields()) > 0){
                    foreach($task->getFields() as $field){
                        $responseData['values'][$field->getID()] = $request->input($field->getID());
                        $validator = $field->getValidator($request->input($field->getID()));
                        if(!$validator->passes()){
                            $responseData['errors'][$field->getID()] = $validator->errors()->all();
                        }
                    }
                }
            }
        }

        if(!empty($responseData['errors'])){
            $responseData['status'] = 'error';
        }else{
            $responseData['status'] = 'success';
        }

        return new JsonResponse($responseData, 200);
    }

    public function postInstall(Request $request, Installer $installer, Dispatcher $dispatcher){

        $output = new BufferedOutput();

        $installer->setFieldValues($request->all());

        $versions = $installer->getVersions();

        foreach($versions as $version){
            $tasks = $installer->getTasksForVersion($version);
            foreach($tasks as $task){
                $output->writeln('<span class="text-info">Running ' . $task->getTitle() . ' Task...</span>');
                try {
                    $task->setInput($installer->getFieldValues());
                    $task->run($output);
                }catch(TaskRunException $e){
                    $output->writeln('<span class="text-danger">'.$e->getMessage().'</span>');
                    return new JsonResponse(['output' => $output, 'status' => 'error'], 200);
                }
                $output->writeln('<span class="text-info">Task ' . $task->getTitle() . ' Completed!</span>');
            }
        }

        $dispatcher->fire(new AfterInstallEvent($installer, $output));

        $installer->saveCompleted();

        $output->writeln('<span class="text-success">Installation Completed!</span>');

        $output = array_filter(explode("\n", $output->fetch()));

        return new JsonResponse(['output' => $output, 'status' => 'success'], 200);
    }

}