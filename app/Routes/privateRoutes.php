<?php
/**
 * The implementation of each single route is not beautiful.
 * Anyway. its more about the middlewares
 */
namespace App\Routes;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;

/*
 * auth middleware
 */
$basePath            = $settings['general_settings']['basePath'];
$authCheckMiddleWare = new \App\Middleware\SentinelAuthCheckMiddleWare($container->get('sentinel'), $basePath);

/*
 * add middleware to check write / read permissions for file based operations
 */
$writePerMissonMiddleware = function (Request $request, RequestHandler $handler) {
    $settings   = $this->get('settings')['general_settings'];
    $dataFolder = $settings['data_folder'];
    $repoFolder = $settings['data_repo_folder'];
    if (!is_writeable($dataFolder) || !is_writeable($repoFolder)) {
        $error    = "data folder $dataFolder or $repoFolder not writeable, check permission";
        $response = new \Slim\Psr7\Response;
        $response = $response->withStatus(400);
        $response->getBody()->write($error);
        return $response;
    }
    $response = $handler->handle($request);
    return $response;
};

/*
 * ROUTES and GROUPS
 * group backend routes, all routes inside need a valid auth cookie
 */
$app->group('/backend', function (RouteCollectorProxy $group) use ($writePerMissonMiddleware) {
    /*
     * responde to options-requests.
     * browser fires these request before all request containing withCredentials
     * all 'backend' routes need to respond to options requests
     * Browsers PREFLIGHT - Options-Request
     */
    $group->options('/[{params:.*}]', function ($request, $response, array $args) {
        return $response;
    });

    /* 
     * config used for many routes
     */
    $settings   = $this->get('settings')['general_settings'];
    $repo_folder = $settings['data_repo_folder'];
    $data_file = $settings['data_file'];
    $data_folder = $settings['data_folder'];
    $current_json_file = $settings['data_folder'] . "/" . $settings['data_file'] . ".json";

    /*
     * returning filenames of files in data_repo_folder
     */
    $group->get('/files', function ($request, $response, $args) use ($repo_folder) {
        $fileList   = glob($repo_folder . '/*.json');
        $cleanNames = array_map(function ($f) use ($repo_folder) {
            $f = str_replace($repo_folder . "/", "", $f);
            return $f;
        }, $fileList);
        $response->getBody()->write(json_encode($cleanNames));
        return $response;
    });

    /*
     * get content of a file via id ( id = filename )
     */
    $group->get('/files/{id}', function ($request, $response, $args) use ($repo_folder)  {
        try {
            $file       = $repo_folder . "/" . $args['id'];
            $content    = file_get_contents($file);
            if($content == false) throw new \Exception("not found", 1);
            $response->getBody()->write($content);
            return $response;
            
        } catch (\Exception $e) {
             $response = $response->withStatus(404);
                $body     = $response->getBody()->write($e->getMessage());
                return $response;
        }
       
    });

    /* content and date for the currently used json file */
    $group->get('/current', function ($request, $response, $args) use ($current_json_file) {
        try {
            $content  = file_get_contents($current_json_file);
            if($content == false) throw new \Exception("no content", 1);
            $out      = [
                "content" => $content,
                "edit"    => date("d.m.Y H:i", filemtime($current_json_file)),
            ];
            $response->getBody()->write(json_encode($out, JSON_UNESCAPED_UNICODE));
            return $response;
        } catch (\Exception $e) {
             $response = $response->withStatus(404);
                $body     = $response->getBody()->write($e->getMessage());
                return $response;
        }
    });

    /*
     * ROUTES NEEDING WRITE PERMISSIONS  
     * ROUTES NEEDING WRITE PERMISSIONS  
     * ROUTES NEEDING WRITE PERMISSIONS  
     * ->add($writePerMissonMiddleware);
     */
    $group->group('', function (RouteCollectorProxy $group) use ($repo_folder, $data_file, $data_folder) {

        $group->post('/save', function ($request, $response, $args) use ($repo_folder, $data_file) {
            try {
                $body       = $request->getParsedBody();
                if(!isset($body['currentEditorData'])) throw new \Exception("wrong params", 1);
                $content    = $body['currentEditorData'];
                $fileName   = $data_file."-" . date("Y-m-d-H-i") . ".json";
                $file       = $repo_folder . "/" . $fileName;
                $success = file_put_contents($file, json_encode($content, JSON_UNESCAPED_UNICODE));
                if($success == false) throw new \Exception("could not write to file", 1);
                $out = [
                    "filename"   => $fileName,
                    "content"    => $content,
                    "dataFolder" => $repo_folder,
                ];
                $response->getBody()->write(json_encode($out));
                $response = $response->withStatus(200);
                return $response;
               
            } catch (\Exception $e) {
                $response = $response->withStatus(400);
                $body     = $response->getBody()->write($e->getMessage());
                return $response;
                
            }
        });

        /*
         * overwrite the current / live file
         */
        $group->post('/setcurrent', function ($request, $response, $args) use ($repo_folder, $data_folder, $data_file) {
            try {
                $body       = $request->getParsedBody();
                if(!isset($body['fileId'])) throw new \Exception("wrong params", 1);
                $fileName     = $body['fileId'];
              
                $file       = $repo_folder . "/" . $fileName;
                $content    = file_get_contents($file);
                if($content == false) throw new \Exception("file not existing", 1);
                
                $destFile     = $data_folder . "/" . $data_file . ".json";

                $success = file_put_contents($destFile, $content);
                if($success == false) throw new \Exception("could not write to file", 1);
                $response = $response->withStatus(200);
                $body     = $response->getBody()->write($fileName);
                return $response;
            } catch (\Exception $e) {
                $response = $response->withStatus(400);
                $body     = $response->getBody()->write($e->getMessage());
                return $response;
            }
           
        });

        /*
         * delete a file
         */
        $group->post('/delete', function ($request, $response, $args) use ($repo_folder) {
            try {
                $body       = $request->getParsedBody();
                if(!isset($body['fileId'])) throw new \Exception("wrong params missing fileId", 1);
                $fileName     = $body['fileId'];
                $file       = $repo_folder . "/" . $fileName;
                if (!file_exists($file)) throw new \Exception("$file file not existing ", 1);
                unlink($file);
                $response = $response->withStatus(200);
                $body     = $response->getBody()->write("$fileName deleted");
                return $response;
            } catch (\Exception $e) {
                $response = $response->withStatus(400);
                $body     = $response->getBody()->write($e->getMessage());
                return $response;
            }
        });
    })->add($writePerMissonMiddleware);
})->add($authCheckMiddleWare); // end backend group
