# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/contrib-jessie64"
  config.vm.network "private_network", ip: "192.168.50.7"
  config.vm.hostname = "stagefb.publicbroadcasting.net"
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.include_offline = true

  config.vm.synced_folder "./", "/pmp_envoy", mount_options: ["dmode=777", "fmode=777"]

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "1024"
  end

  # install some base packages
  config.vm.provision :shell, path: "provision.sh"
end
