docker-compose down

rm -rf mysql/db/data
rm -rf storage/logs

mkdir -p mysql/db/data
mkdir -p storage/logs

./build-docker.sh