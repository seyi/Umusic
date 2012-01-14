MAX=10000
OFFSET=0
for i in {0..99}
do
	php index.php --uri=conversion/generate_tracks --max=$MAX --offset=$OFFSET
	let OFFSET=$OFFSET+$MAX
done