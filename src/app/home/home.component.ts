import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { RecaptchaModule } from 'ng-recaptcha';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';
import { NgxSpinnerModule } from 'ngx-spinner';
import { NgxSpinnerService } from 'ngx-spinner';

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
            ReactiveFormsModule,
            NgxSpinnerModule
            ],
  providers: [UserService],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {
  @ViewChild('about') aboutDiv!: ElementRef;
  isContactVisible: boolean = false; 
  userForm!: FormGroup;
  messageSent: boolean = false;
  captchaResolved: boolean = false; // Variable to track reCAPTCHA status
  captchaResponse: string | null = null;
  constructor(private fb: FormBuilder, 
    private userService: UserService, 
    private http: HttpClient,
    private router: Router,
    private spinner: NgxSpinnerService,) { }
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
  scrollToAbout() {
    const element = this.aboutDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }
  toggleContact() {
    this.isContactVisible = !this.isContactVisible; 
  } 
  closeContact() {
    this.isContactVisible = false; 
  }
  resolved(captchaResponse: string | null) { 
    console.log('Captcha Response:', captchaResponse); 
    this.captchaResponse = captchaResponse;
    this.captchaResolved = !!captchaResponse; // Set to true if captchaResponse is not empty 
  }
  onSubmit(){
    if(this.userForm.valid && this.captchaResolved){
      const captchaToken = this.captchaResponse ?? ''; 
      this.userService.sendHomeContact(this.userForm.value, captchaToken).subscribe(
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
  downloadFlyer() { 
    this.spinner.show();
    const fileUrl = '../../assets/Flyer_PVA.pdf'; 
    // Replace with your file URL 
    this.http.get(fileUrl, { responseType: 'blob' }).subscribe((response: Blob) => { 
      const downloadURL = window.URL.createObjectURL(response); 
      const link = document.createElement('a'); 
      link.href = downloadURL; 
      link.download = 'PVA Flyer.pdf'; // Replace with the desired file name 
      link.click(); 
      this.spinner.hide();
    }, 
    error => { 
      console.error('Error downloading the file', error); 
      this.spinner.hide();
    }); 
  }
}
