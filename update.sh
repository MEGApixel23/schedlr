# Init values
version=$(git rev-parse HEAD)
dest=../app
curr=$PWD

# # Stopping container
cd $dest
if [ -f docker-compose.yml ]; then
	docker-compose kill
fi
cd $curr

# Copy files
rm -rf $dest/$version && mkdir $dest/$version
cp -r deploy $dest/deploy
cp -r src/* $dest/$version
cp docker-compose.yml $dest

# Put given arguments to .env file
for ARG in "$@"; do
    echo $ARG >> $dest/$version/.env
done

# Prepare docker-compose config
sed -i 's/-\s.\/src:/- .\/'$version':/g' $dest/docker-compose.yml

# # Building container
cd $dest
docker-compose up -d
database_container=$(docker ps -f "name=application" --format '{{.Names}}')
docker exec $database_container composer install
docker exec $database_container ./cli.php migrate
