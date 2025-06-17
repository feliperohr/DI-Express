<?php 
   header('Content-Type: application/json'); // Define o cabeçalho para JSON
   $response = ['status' => '', 'message' => ''];
   
   $APP='/var/www/DIExpress';
   $CREDITOS="\nDesign de Abertura \nIgor Reszka Pinheiro\n\nDesenvolvedores da ferramenta\nTiago Luiz Schmitz";
   require '../../vendor/autoload.php';
// Verify the request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.1 405 Method Not Allowed');
        exit;
    }      
    try {   
        $ffmpeg = FFMpeg\FFMpeg::create();
        $inputs = array(
            $APP.'/assets/vinhetas/pre'.$_POST['curso'].'.mp4',
            $_FILES["arquivoVideo"]["tmp_name"],
            $APP.'/assets/creditos.png',
            $APP.'/assets/marcadagua.png',
            $APP.'/assets/creditos.mp3'
        );
        $advancedMedia = $ffmpeg->openAdvanced($inputs);
        $advancedMedia->filters()
            ->custom('[0]','scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2,fps=30,setsar=1','[v0]')
            ->custom('[1]','scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2,fps=30,setsar=1','[v1]')
            ->custom('[2]','scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2,fps=30,setsar=1','[v2]')
            ->custom('[v2]','loop=450:size=450:start=0','[bgcred]')
            //creditos
            ->custom('[bgcred]',"drawtext=enable='between(t,0,15)':fontfile=/path/to/font.ttf:text='".$_POST['creditos'].$CREDITOS."':".
                                "x='(w-text_w)/2':y='720-(720+text_h)*t/15':fontcolor=white:fontsize=32",'[credmut]')
            //marca dagua
            ->custom('[v1][3]','overlay=(main_w-overlay_w-30):(main_h-overlay_h-10)','[vf]')
            //melhoria de audio
            ->custom('[1:a:0]','pan=1c|c0=c0+c1','[at0]')
            ->custom('[at0]','arnndn=m='.$APP.'/assets/modelrnn/mp.rnnn','[at1]')
            ->custom('[at1]','highpass=f=80,lowpass=f=10000','[at2]')
            ->custom('[at2]','loudnorm=I=-16:LRA=7:TP=-2','[at3]') 
            ->custom('[at3]','arnndn=m='.$APP.'/assets/modelrnn/mp.rnnn','[at4]')
            //compilacao das partes
            ->custom('[v0][0:a:0][vf][at4][credmut][4:a:0]','concat=n=3:v=1:a=1','[outv][outa]');

        $format = new FFMpeg\Format\Video\X264();
        $format->setAudioCodec("libmp3lame");
        $format->setInitialParameters(['-hwaccel', 'cuda']);
                
        $format->on('progress', function ($video, $format, $percentage) {
            // echo "$percentage % Finalizado <br>";
        });
            $advancedMedia
                ->map(array('[outv]','[outa]'), $format, $APP.'/output/'.$_POST["cpf"].'_'.$_FILES["arquivoVideo"]["name"])
                ->save();
                $response['status'] = 'success';
                $response['message'] = 'Na base do suor dos gnomos finalizamos a edição do Video.';            
    } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Algo de errado nao esta certo.';
            //echo json_encode($response);
        }
    echo json_encode($response);
?>
