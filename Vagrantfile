Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.provider "virtualbox" do |vb|
    vb.customize [ "modifyvm", :id, "--uartmode1", "disconnected", "--memory", 2048 ]
  end
  config.vm.provision :shell, path: "vagrant-setup/bootstrap.sh"
  config.vm.network :forwarded_port, guest: 80, host: 8080
end
