# AWS Production Deployment Architecture Guide

This document outlines the recommended enterprise cloud hosting architecture on Amazon Web Services (AWS) for the **Jajal Medical Case Management** application, meeting medical device security, HIPAA/GDPR data protection standards, and high-availability operational requirements.

---

## Architecture Overview

```text
               [ Internet / Users ]
                        │
                        ▼ (HTTPS :443)
              [ AWS Route 53 (DNS) ]
                        │
                        ▼
       [ AWS Certificate Manager (ACM SSL) ]
                        │
                        ▼
        [ Application Load Balancer (ALB) ]
            (Public Subnets in VPC)
                        │
       ┌────────────────┴────────────────┐
       ▼                                 ▼
[ EC2: App Node A ]              [ EC2: App Node B ]
(Private Subnet AZ-1)            (Private Subnet AZ-2)
  Nginx + PHP 8.3-FPM              Nginx + PHP 8.3-FPM
       │                                 │
       ├─────────────────┬───────────────┤
       ▼                 ▼               ▼
[ Amazon RDS Multi-AZ ]  [ AWS Secrets ]  [ Amazon S3 ]
(MySQL 8.0 - Private)     (App Secrets)    (Encrypted CAD/Scans)
```

---

## Infrastructure Components

### 1. Compute Layer: Amazon EC2 & Auto Scaling
- **Instances**: Deploy `t4g.medium` or `c7g.large` AWS Graviton3 EC2 instances in an **Auto Scaling Group (ASG)** across at least two Availability Zones (AZs) inside a private VPC subnet.
- **Web Server Stack**: Nginx reverse proxy with PHP 8.3-FPM, supervised with `systemd` or containerized with AWS ECS/Fargate.
- **Queue Workers**: Background job workers (`php artisan queue:work`) managed by `supervisor`.

### 2. Database Layer: Amazon RDS for MySQL (Multi-AZ)
- **Engine**: MySQL 8.0 Enterprise.
- **High Availability**: Multi-AZ standby deployment with automatic synchronous replication and instant failover.
- **Security & Encryption**: 
  - Placed strictly in isolated DB subnets with no public internet routing.
  - Storage encrypted at rest using **AWS KMS** (AES-256).
  - Enforced SSL/TLS connections for all database communications (`PDO::MYSQL_ATTR_SSL_CA`).
- **Automated Backups**: Continuous daily automated snapshots with Point-In-Time Recovery (PITR) retained for 35 days.

### 3. Storage Layer: Amazon S3 (Medical Scans & Implant CAD Files)
- Dedicated S3 bucket for patient radiographic CT scans, CAD mesh models, and surgical export logs.
- **Security**: Default AWS KMS server-side encryption (`aws:kms`), bucket policy strictly blocking public access, and Pre-Signed S3 URLs generated with short 15-minute expirations for authorized staff.

### 4. Network Security & Security Groups
Configure strict least-privilege security groups:
- **ALB Security Group**:
  - Inbound: Port 443 (HTTPS) from `0.0.0.0/0`.
  - Inbound: Port 80 (HTTP) redirecting immediately to HTTPS 443.
  - Outbound: Forward to EC2 Security Group on Port 80/443.
- **EC2 Security Group**:
  - Inbound: Port 80/443 only from ALB Security Group.
  - Inbound: Port 22 (SSH) only via **AWS Systems Manager (SSM) Session Manager** (no open bastion ports).
  - Outbound: All traffic via NAT Gateway.
- **RDS Security Group**:
  - Inbound: Port 3306 only from the EC2 Security Group.
  - Outbound: Blocked.

### 5. Secrets Management & Environment Isolation
- Production credentials (`APP_KEY`, `DB_PASSWORD`, API secrets) are never committed to Git or stored in raw files.
- Stored securely in **AWS Secrets Manager** or **AWS Systems Manager Parameter Store** (SecureString).
- During deployment, EC2 instances retrieve secrets directly into memory via IAM Instance Roles.

### 6. Domain, HTTPS & SSL/TLS Termination
- **DNS**: Managed via **Amazon Route 53** with latency and health-check routing.
- **SSL Certificate**: Free, auto-renewing TLS certificates provisioned through **AWS Certificate Manager (ACM)**.
- **Termination**: TLS terminated at the Application Load Balancer with modern TLS 1.3/1.2 security policies (`ELBSecurityPolicy-TLS13-1-2-2021-06`), enforcing HSTS headers.

---

## Deployment & CI/CD Pipeline
1. **GitHub Actions / GitLab CI**:
   - Automated pull-request checks: PHPUnit tests, Pint styling, security scanning with Trivy.
2. **Zero-Downtime Deployment**:
   - Deployment via AWS CodeDeploy or Laravel Envoyer:
     ```bash
     composer install --no-dev --optimize-autoloader
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     php artisan migrate --force
     php artisan queue:restart
     ```
3. **Observability**: Centralized logs streamed to **Amazon CloudWatch Logs** with alarms triggered on 5xx error rate spikes or anomalous latency.
