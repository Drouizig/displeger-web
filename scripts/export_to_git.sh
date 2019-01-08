SYMFONY_DIR="/var/www/displeger-web"
DATA_DIR="/var/www/displeger-verbou"

cd ${DATA_DIR}
git checkout web-export
git pull origin web-export
${SYMFONY_DIR}/bin/console app:export ${DATA_DIR}/data/tmp.csv
${DATA_DIR}/scripts/urzhian.py ${DATA_DIR}/data/tmp.csv > ${DATA_DIR}/data/displeger_format.csv
git add ${DATA_DIR}/data/displeger_format.csv
git commit -m 'Update data from web'
git push origin web-export