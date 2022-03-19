rm -rf mysql/db/data
rm -rf storage/logs

mkdir -p mysql/db/data
mkdir -p storage/logs

# compose up
docker-compose build
docker-compose up