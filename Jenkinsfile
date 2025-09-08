pipeline {
    agent any

    environment {
        SSH_TARGET = "${VM_USERNAME_ADS_03}@${VM_IP_ADS_03}"
    }

    stages {
        stage('Initialize Deployment') {
            steps {
                script {
                    echo "Initializing deployment process..."

                    if (!env.VM_IP_ADS_03) {
                        error "Error: VM_IP environment variable is not set."
                    }
                    if (!env.VM_USERNAME_ADS_03) {
                        error "Error: VM_USERNAME environment variable is not set."
                    }
                    if (!env.APP_PATH_ADS_03) {
                        error "Error: APP_PATH environment variable is not set."
                    }

                    env.VM_IP = env.VM_IP_ADS_03
                    env.VM_USERNAME = env.VM_USERNAME_ADS_03
                    env.APP_PATH = env.APP_PATH_ADS_03

                    env.REPO_NAME = env.GIT_URL.tokenize('/').last().replace('.git', '')
                    echo "Initialized repo name: ${env.REPO_NAME}"

                    def setupCommand = """
                    ssh ${SSH_TARGET} bash -c '
                    set -e
                    echo "Connected to ${VM_IP}. Setting up environment..."

                    if [ ! -d "${APP_PATH}" ]; then
                        echo "Application directory does not exist. Creating..."
                        mkdir -p ${APP_PATH}
                    fi

                    if [ -d "${APP_PATH}/${env.REPO_NAME}" ]; then
                        echo "Changing ownership of ${APP_PATH} to current user..."
                        sudo chown -R \$(whoami):users ${APP_PATH}/${env.REPO_NAME}
                    fi
                    '
                    """
                    sh setupCommand

                    echo "Initialization completed successfully."
                }
            }
        }

        stage('Fetch Source Code') {
            steps {
                script {
                    echo "Fetching source code on the server..."

                    def fetchCodeCommand = """
                    ssh ${SSH_TARGET} bash -c '
                    set -e
                    cd ${APP_PATH}

                    if [ -d "${env.REPO_NAME}" ]; then
                        echo "Repository already exists. Updating source code..."
                        cd ${env.REPO_NAME}
                        git fetch --all
                        git reset --hard origin/main
                        git submodule update --init --recursive --remote
                    else
                        echo "Cloning repository..."
                        git clone --recurse-submodules ${env.GIT_URL}
                    fi
                    '
                    """
                    sh fetchCodeCommand
                    echo "Source code updated or cloned successfully."
                }
            }
        }

        stage('Deploy Environment Variables') {
            steps {
                script {
                    echo "Deploying .env file to server..."

                    withCredentials([file(credentialsId: 'fb173d0c-0b6a-4a55-93a9-a72304ad99bc', variable: 'ENV_FILE')]) {
                        def sshPrepCommand = """
                        ssh ${SSH_TARGET} bash -c '
                        set -e
                        cd ${APP_PATH}/${env.REPO_NAME}

                        if [ -f .env ]; then
                            echo "Existing .env file found. Removing..."
                            rm .env
                        else
                            echo "No existing .env file found."
                        fi
                        '
                        """

                        echo "Preparing environment on server..."
                        sh label: 'Clean .env on remote', script: sshPrepCommand

                        def deployEnvCommand = "scp ${ENV_FILE} ${SSH_TARGET}:${APP_PATH}/${env.REPO_NAME}/.env"
                        sh label: 'Upload .env to remote', script: deployEnvCommand

                        def fixEnvPermissions = """
                        ssh ${SSH_TARGET} bash -c '
                            chmod 644 ${APP_PATH}/${env.REPO_NAME}/.env
                            chown ${env.VM_USERNAME_ADS_03}:${env.VM_USERNAME_ADS_03} ${APP_PATH}/${env.REPO_NAME}/.env
                        '
                        """
                        sh label: 'Change .env permission', script: fixEnvPermissions
                    }
                    echo ".env file deployed successfully."
                }
            }
        }

        stage('Build & Deploy Services') {
            steps {
                script {
                    echo "Building Docker images and deploying services on the server..."
                    def deployCommand = """
                    ssh ${SSH_TARGET} bash -c '
                    set -e
                    cd ${APP_PATH}/${env.REPO_NAME}

                    # Build fresh Docker images to bake in latest code
                    echo "Building app image..."
                    sudo docker build -t pspa_scm -f docker/php/Dockerfile.prod .

                    echo "Building nginx image..."
                    sudo docker build -t nginx-pspa_scm -f docker/nginx/Dockerfile.prod .

                    # Deploy or update Docker Compose stack
                    echo "Deploying with Docker Compose..."
                    sudo docker compose down
                    sudo docker compose -f docker-compose.prod.yml up -d --remove-orphans
                    '
                    """
                    sh deployCommand
                    echo "Services built and deployed successfully."
                }
            }
        }
    }

    post {
        success {
            script {
                echo "Pipeline completed successfully. Reverting ownership to www-data..."
                def revertOwnershipCommand = """
                ssh ${SSH_TARGET} bash -c '
                set -e
                if [ -d "${APP_PATH_ADS_03}/${env.REPO_NAME}" ]; then
                    sudo chown -R www-data:www-data ${APP_PATH_ADS_03}/${env.REPO_NAME}
                fi
                '
                """
                sh revertOwnershipCommand
            }
        }
        failure {
            script {
                echo "Pipeline failed. Reverting ownership to www-data..."
                def revertOwnershipCommand = """
                ssh ${SSH_TARGET} bash -c '
                set -e
                if [ -d "${APP_PATH_ADS_03}/${env.REPO_NAME}" ]; then
                    sudo chown -R www-data:www-data ${APP_PATH_ADS_03}/${env.REPO_NAME}
                fi
                '
                """
                sh revertOwnershipCommand
            }
        }
    }
}
