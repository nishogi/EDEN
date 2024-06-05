### variables.auto.tfvars
clone_node_name        = "atlantis"
clone_vm_id            = 4001
cloudinit_dns_domain   = "atlantis.int-evry.fr"
cloudinit_dns_servers  = ["9.9.9.9"]
cloudinit_ssh_keys     = ["ssh_key_eee"]
cloudinit_user_account = "user"
datastore_id           = "local-lvm"
disk_file_format       = "raw"
node_name              = "atlantis"
pve_api_token          = "root@pam!tofu_token2_root=f07a58b7-bd81-4180-a92c-9798d02b6f1b"
pve_host_address       = "https://157.159.104.154:8006"
tmp_dir                = "/tmp"
vm_bridge_lan          = "vmbr2"
vm_cpu_cores_number    = 2
vm_cpu_type            = "x86-64-v2-AES"
vm_description         = "Managed by terraform."
vm_disk_size           = 64
vm_id                  = 45
vm_memory_max          = 8192
vm_memory_min          = 4096
vm_name                = "NET4101-mwilliot-1"
vm_socket_number       = 1
create_new_vm          = false
ip                     = "192.168.5.233/16"
