cd /home/chaiqpso/rabedo_app
pkill -9 -f node
rm -rf .next node_modules
unzip -o rabedo-deploy.zip
chmod -R 755 .next public