pipeline {
  agent any
  stages {
    stage('Build dependencies') {
      agent {
        dockerfile {
          filename 'Dockerfile'
        }

      }
      steps {
        sh 'composer install'
      }
    }
    stage('Linters') {
      agent {
        dockerfile {
          filename 'Dockerfile'
        }

      }
      steps {
        sh 'composer cs:check'
      }
    }
    stage('Setting environment') {
      steps {
        sh '''cp .env.ci .env
'''
      }
    }
    stage('Test unit') {
      parallel {
        stage('Test unit') {
          agent {
            dockerfile {
              filename 'Dockerfile'
            }

          }
          steps {
            sh 'composer test:unit'
          }
        }
        stage('Test API') {
          agent {
            dockerfile {
              filename 'Dockerfile'
            }

          }
          steps {
            sh 'composer test:api'
          }
        }
        stage('Test external-service') {
          agent {
            dockerfile {
              filename 'Dockerfile'
            }

          }
          steps {
            sh 'composer test:external-service'
          }
        }
      }
    }
    stage('Deploy to staging') {
      agent any
      steps {
        sh 'composer deploy:staging'
      }
    }
  }
}