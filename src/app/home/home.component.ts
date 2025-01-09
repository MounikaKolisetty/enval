import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { MatIconModule } from '@angular/material/icon';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { RecaptchaModule } from 'ng-recaptcha';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavbarComponent, 
            MatIconModule,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            RecaptchaModule,
            CommonModule,
            HttpClientModule,
            ScrollButtonComponent,
            ReactiveFormsModule
            ],
  providers: [UserService],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {
  isContactVisible: boolean = false; 
  userForm!: FormGroup;
  messageSent: boolean = false;
  captchaResolved: boolean = false; // Variable to track reCAPTCHA status
  constructor(private fb: FormBuilder, private userService: UserService, private http: HttpClient) { }
  ngOnInit(){
    this.userForm = this.fb.group({
      name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      corporateTraining: [false], 
      trainingForPractitioners: [false], 
      consulting: [false], 
      projects: [false], 
      subscribe: [false],
      message: ['', Validators.required]
    })
  }
  toggleContact() {
    this.isContactVisible = !this.isContactVisible; 
  } 
  closeContact() {
    this.isContactVisible = false; 
  }
  resolved(captchaResponse: string | null) { 
    console.log('Captcha Response:', captchaResponse); 
    this.captchaResolved = !!captchaResponse; // Set to true if captchaResponse is not empty 
  }
  onSubmit(){
    if(this.userForm.valid && this.captchaResolved){
      this.userService.sendHomeContact(this.userForm.value).subscribe(
        response => {
          console.log('Email sent successfully', response); 
          this.messageSent = true;
          this.userForm.reset();
        }, 
        error => { 
          console.error('Error sending email', error); 
        }
      );
    }
  }
  downloadFile() { 
    const fileUrl = '../../assets/Corporate Brochure.pdf'; 
    // Replace with your file URL 
    this.http.get(fileUrl, { responseType: 'blob' }).subscribe((response: Blob) => { 
      const downloadURL = window.URL.createObjectURL(response); 
      const link = document.createElement('a'); 
      link.href = downloadURL; 
      link.download = 'Corporate Brochure.pdf'; // Replace with the desired file name 
      link.click(); 
    }, 
    error => { 
      console.error('Error downloading the file', error); 
    }); 
  }
}
