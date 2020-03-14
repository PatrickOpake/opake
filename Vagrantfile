# -*- mode: ruby -*-
# vi: set ft=ruby :

Dir.glob('Vagrantfile.local').sort.each do |path|
  load path
  return
end

Vagrant.configure("2") do |config|

  # Box
  config.vm.box = "ubuntu/xenial64"
  config.vm.box_url = "ubuntu/xenial64"
  config.vm.hostname = "Opake-Xenial64"
  config.vm.define "opk"

  config.vm.network "private_network", ip: "33.33.33.33"

  # Shared folders
  config.vm.synced_folder "./", "/srv/opake", type: "nfs", mount_options: ['rw', 'vers=3', 'tcp', 'fsc' ,'actimeo=1']

  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
  config.vm.provision "shell", path: "./vagrant/provision.sh"

  config.vm.provider "virtualbox" do |vb|
    vb.memory = 1024
    vb.cpus = 1
  end

end
