pipeline {
  agent {
    dockerfile {
      filename 'Dockerfile'
    }
  }

  stages {
  
    stage('Setting environment') {
      steps {
        sh 'cp .env.ci .env'
      }
    }

    stage('Build dependencies') {
      steps {
        sh 'composer install'
      }
    }

    stage('Linters') {
      steps {
        sh 'composer cs:check'
      }
    }

    stage('Test unit') {      
      parallel {
        stage('Test unit') {
          steps {
            sh 'composer test:unit'
          }
        }

        stage('Test API') {
          steps {
            sh 'composer test:feature'
          }
        }

        stage('Test external-service') {
          steps {
            sh 'composer test:external'
          }
        }
      }
    }

    stage('Deploy') {
      parallel {      
        stage('Deploy to staging') {
          when {
            branch 'development'
          }
          steps {
            sh 'php artisan deploy'
          }
        }
        
        stage('Deploy to production') {
          when {
            branch 'master'
          }
          steps {
            dir('endpoint-test') {
            sh 'php artisan deploy'
          }
        }
      }
    }
  }
}
}
