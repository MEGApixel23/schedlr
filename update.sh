# Init values
version=$(git rev-parse HEAD)
dest=../app
curr=$PWD

# # Stopping container
cd $dest
docker-compose kill
cd $curr

# Copy files
rm -rf $dest/$version && mkdir $dest/$version
cp -r deploy $dest/deploy
cp -r src/* $dest/$version
cp docker-compose.yml $dest

# Prepare docker-compose config
sed -i 's/-\s.\/src:/- .\/'$version':/g' $dest/docker-compose.yml

# # Building container
cd $dest
docker-compose up -d
