### vm.tf

resource "proxmox_virtual_environment_vm" "user_example2" {
  count           = var.create_new_vm ? 2 : 1
  description     = var.vm_description
  keyboard_layout = "fr"
  machine         = "q35"
  migrate         = true
  name            = var.vm_name
  node_name       = var.node_name
  scsi_hardware   = "virtio-scsi-single"
  started         = false
  tablet_device   = false
  vm_id           = var.vm_id

  clone {
    datastore_id = var.datastore_id
    full         = true
    node_name    = var.clone_node_name
    retries      = 2
    vm_id        = var.clone_vm_id
  }

  agent {
    enabled = true
    trim    = true
  }

  cpu {
    cores   = var.vm_cpu_cores_number
    numa    = true
    sockets = var.vm_socket_number
    type    = var.vm_cpu_type
  }

  disk {
    datastore_id = var.datastore_id
    discard      = "on"
    file_format  = var.disk_file_format
    interface    = "scsi0"
    iothread     = true
    size         = var.vm_disk_size
  }

  initialization {
    datastore_id = var.datastore_id
    dns {
      domain  = var.cloudinit_dns_domain
      servers = var.cloudinit_dns_servers
    }
    ip_config {
      ipv4 {
        address = "157.159.11.219/24"
        gateway = ""
      }
    }
    user_account {
      keys     = var.cloudinit_ssh_keys
      username = var.cloudinit_user_account
    }
  }

  memory {
    dedicated = var.vm_memory_max
    floating  = var.vm_memory_min
  }

  network_device {
    bridge = var.vm_bridge_lan
    model  = "virtio"
    
  }

  operating_system {
    type = "l26"
  }

  startup {
    order      = "1"
    up_delay   = "20"
    down_delay = "20"
  }

  serial_device {}
  
}
