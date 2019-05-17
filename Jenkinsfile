pipeline {
  agent {
    dockerfile {
      filename 'Dockerfile'
    }

  }
  stages {
    stage('Setting environment') {
      steps {
        sh '''cp .env.ci .env
  '''
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
            sh 'composer test:api'
          }
        }
        stage('Test external-service') {
          steps {
            sh 'composer test:external-service'
          }
        }
      }
    }
    stage('Deploy to staging') {
      steps {
        sh 'composer deploy:staging'
      }
    }
  }
}