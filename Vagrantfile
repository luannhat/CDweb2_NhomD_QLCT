Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/focal64"
  config.vm.network "private_network", ip: "192.168.33.10"

  # Gắn đúng thư mục chứa project
  config.vm.synced_folder "./sources/11-training-php", "/var/www/html"

  config.vm.provider "virtualbox" do |vb|
    vb.memory = 2048
    vb.cpus = 2
  end

  # Cài Apache + PHP
  config.vm.provision "shell", inline: <<-SHELL
    apt-get update
    apt-get install -y apache2 php libapache2-mod-php
    systemctl enable apache2
    systemctl restart apache2
  SHELL


  config.vm.boot_timeout = 900
end

