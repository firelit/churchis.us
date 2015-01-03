Vagrant.configure("2") do |config|

  config.vm.box = "chef/centos-7.0"

  config.vm.network "forwarded_port", guest: 80, host: 8090, auto_correct: true
  
  config.vm.provision "shell", inline: "curl -sS http://dl.fedoraproject.org/pub/epel/7/x86_64/e/epel-release-7-5.noarch.rpm > epel-release-7.noarch.rpm" # Adding repo for php-mcrypt
  config.vm.provision "shell", inline: "rpm -Uvh epel-release-7.noarch.rpm"

  # config.vm.provision "shell", inline: "yum update -y"

  config.vm.provision "shell", inline: "yum install -y httpd"
  config.vm.provision "shell", inline: "yum install -y php"
  config.vm.provision "shell", inline: "yum install -y php-mbstring php-mcrypt php-mysql php-pdo php-soap"
	
  config.vm.provision "shell", inline: "ln -s /vagrant/docs/churchis_apache.conf /etc/httpd/conf.d/churchis_apache.conf"
  config.vm.provision "shell", inline: "sed -i 's/AllowOverride None/AllowOverride All/g' /etc/httpd/conf/httpd.conf"
  config.vm.provision "shell", inline: "sed -i 's/display_errors = Off/display_errors = On/g' /etc/php.ini"
  config.vm.provision "shell", inline: "echo 'date.timezone = America/New_York' >> /etc/php.ini"

  config.vm.provision "shell", inline: "rm -rf /var/www/html"
  config.vm.provision "shell", inline: "ln -s /vagrant /var/www/html"

  config.vm.provision "shell", inline: "systemctl start httpd.service"
  config.vm.provision "shell", inline: "systemctl enable httpd.service"

end