DIEXPRESS=/home/tiago/rotina_di_express

ffmpeg -i "$DIEXPRESS/assets/pre.mp4" -i "/home/tiago/rotina_di_express/teste/teste.mp4" -i "$DIEXPRESS/assets/creditos.png" -i "$DIEXPRESS/assets/marcadagua.png"  -filter_complex "
                            [0]scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2,fps=30,setsar=1[v0];
                            [1]scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2,fps=30,setsar=1[v1];
                            [2]scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2,fps=30,setsar=1[v2]; 
                            [v2]loop=450:size=450:start=0[bgcred];
                            [bgcred]drawtext=enable='between(t,0,15)':fontfile=/path/to/font.ttf:textfile=/home/tiago/rotina_di_express/teste/teste.creditos:
                            x='(w-text_w)/2':
                            y='720-(720+text_h)*t/15':
                            fontcolor=white:fontsize=32[credmut];
                            flite=textfile='/home/tiago/rotina_di_express/teste/teste.creditos':voice=awb:ln=pt[credaud];
                            [v1][3]overlay=(main_w-overlay_w-30):(main_h-overlay_h-10)[vf];
                            [1:a:0]pan=1c|c0=c0+c1[at0];
                            [at0]arnndn=m=$DIEXPRESS/modelrnn/mp.rnnn[at1];
                            [at1]highpass=f=80,lowpass=f=10000[at2];
                            [at2]loudnorm=I=-16:LRA=7:TP=-2[at3]; 
                            [at3]arnndn=m=$DIEXPRESS/modelrnn/mp.rnnn[at4];
                            [v0][0:a:0][vf][at4][credmut][credaud]concat=n=3:v=1:a=1[outv][outa]" -map "[outv]" -map "[outa]" "out_pronto.mp4"       