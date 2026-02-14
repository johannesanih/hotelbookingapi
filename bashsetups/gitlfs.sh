sudo apt update
sudo apt install git-lfs
git lfs install

git lfs track "*.psd"  # example if you want to track PSDs
git add .gitattributes
git commit -m "Configure Git LFS"
