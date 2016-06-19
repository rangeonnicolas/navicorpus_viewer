Ubuntu 14

mkdir /usr/local/app/navicorpus
cd /usr/local/app/navicorpus
git clone https://github.com/rangeonnicolas/navicorpus_viewer.git




sudo nano /etc/apache2/sites-available/navicorpus_viewer.conf

<VirtualHost *:80>
        DocumentRoot /usr/local/app/navicorpus/navicorpus_viewer/     

        <Directory "/usr/local/app/navicorpus/navicorpus_viewer">
                Order deny,allow
                Allow from all
                Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error_navicorpus_viewer.log
        CustomLog ${APACHE_LOG_DIR}/access_navicorpus_viewer.log
</VirtualHost>

sudo a2ensite navicorpus_viewer.conf
sudo service apache2 restart

sudo apt-get install postgresql ????

cd navicorpus_viewer ?????
cd sample_data
psql -U postgres -f ./script_import.sql









The primary part of this work comes from the project GexfJS : http://github.com/raphv/gexf-js

improvements on ththe PHP code (good practises, etc...) are very welcomed (and encourraged), I wrote this code while learning PHP, so it's like a beginner code...!
