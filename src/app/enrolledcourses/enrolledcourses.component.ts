import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { UserService } from '../services/user.service';

@Component({
  selector: 'app-enrolledcourses',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent
  ],
  providers: [UserService],
  templateUrl: './enrolledcourses.component.html',
  styleUrl: './enrolledcourses.component.css'
})
export class EnrolledcoursesComponent {
  userFullName:string = '';
  constructor(private userService:UserService){}
  ngOnInit(){
    this.userFullName = localStorage.getItem('UserName') || '';
  }
  courses(){
    this.userService.getUserCourses().subscribe(
      response => {
        console.log(response)
      }
    )
  }
}
