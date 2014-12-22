Vagrant.configure("2") do |config|

  config.vm.box = "chef/centos-7.0"

  config.vm.synced_folder "./", "/var/www/html"

  config.vm.network "forwarded_port", guest: 80, host: 8090, auto_correct: true

  config.vm.provision "shell", inline: "yum update -y"

  config.vm.provision "shell", inline: "yum install -y httpd"
  config.vm.provision "shell", inline: "yum install -y php"
  config.vm.provision "shell", inline: "yum install -y php-mbstring php-mcrypt php-mysql php-pdo php-soap"
	
  config.vm.provision "shell", inline: "ln -s /vagrant/docs/churchis_apache.conf /etc/httpd/conf.d/churchis_apache.conf"

  config.vm.provision "shell", inline: "systemctl start httpd.service"
  config.vm.provision "shell", inline: "systemctl enable httpd.service"

end